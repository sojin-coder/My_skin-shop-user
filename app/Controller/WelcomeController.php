<?php

class WelcomeController extends Controller
{
    public function index()
    {
        // ពិនិត្យមើលថាតើអ្នកប្រើបាន Login ឬអត់
        if (empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $db = Database::connection();

        // ១. រាប់ចំនួន Order សរុបរបស់អតិថិជននេះ
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM orders WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $totalOrders = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // ២. រាប់ចំនួនទំនិញក្នុង Wishlist
        // សន្មតថាអ្នកមានតារាង wishlist_items ដែលភ្ជាប់ជាមួយ wishlists របស់ user
        $stmt = $db->prepare("
            SELECT COUNT(*) AS total 
            FROM wishlist_items wi
            JOIN wishlists w ON wi.wishlist_id = w.id
            WHERE w.user_id = ?
        ");
        $stmt->execute([$customerId]);
        $wishlistCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // ៣. រាប់ចំនួន Feedback ដែលអតិថិជនបានសរសេរ
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM reviews WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $feedbackCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // ៤. ទាញយក Order ចុងក្រោយបំផុត ៥ (Recent Orders)
        $stmt = $db->prepare("
            SELECT * FROM orders 
            WHERE customer_id = ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute([$customerId]);
        $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ៥. ទាញយក Settings សម្រាប់ Sidebar (Logo & Name)
        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        // បញ្ជូនទិន្នន័យទៅ View
        $this->view('welcome', [
            'totalOrders' => $totalOrders,
            'wishlistCount' => $wishlistCount,
            'feedbackCount' => $feedbackCount,
            'recentOrders' => $recentOrders,
            'settingsByKey' => $settingsByKey
        ]);
    }

    public function profile()
    {
        if (empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['customer_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        $this->view('customer/profile', [
            'user' => $user,
            'settingsByKey' => $settingsByKey
        ]);
    }

    // រក្សាទុកការកែប្រែ Profile
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/customer/profile');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $db = Database::connection();

        // ពិនិត្យមើលថាតើ Email ស្ទួនជាមួយអ្នកផ្សេងឬអត់
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $customerId]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'This email is already used by another account.';
            header('Location: ' . BASE_URL . '/customer/profile');
            exit;
        }

        // ធ្វើបច្ចុប្បន្នភាពព័ត៌មាន
        $stmt = $db->prepare("
            UPDATE users 
            SET name = ?, email = ?, phone = ?, address = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$name, $email, $phone, $address, $customerId]);

        // ធ្វើបច្ចុប្បន្នភាព Session
        $_SESSION['customer_name'] = $name;
        $_SESSION['customer_email'] = $email;

        $_SESSION['success'] = 'Profile updated successfully!';
        header('Location: ' . BASE_URL . '/customer/profile');
        exit;
    }

    // ប្តូរលេខសម្ងាត់
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/customer/profile');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $db = Database::connection();
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$customerId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'Current password is incorrect.';
            header('Location: ' . BASE_URL . '/customer/profile');
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters.';
            header('Location: ' . BASE_URL . '/customer/profile');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New passwords do not match.';
            header('Location: ' . BASE_URL . '/customer/profile');
            exit;
        }

        $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $customerId]);

        $_SESSION['success'] = 'Password changed successfully!';
        header('Location: ' . BASE_URL . '/customer/profile');
        exit;
    }
    // បង្ហាញបញ្ជី Order ទាំងអស់របស់អតិថិជន
    public function orders()
    {
        if (empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $db = Database::connection();

        // ទាញយក Order ទាំងអស់របស់គាត់
        $stmt = $db->prepare("
            SELECT * FROM orders 
            WHERE customer_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$customerId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Settings សម្រាប់ Sidebar
        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        $this->view('customer/orders', [
            'orders' => $orders,
            'settingsByKey' => $settingsByKey
        ]);
    }

    // បង្ហាញព័ត៌មានលម្អិតនៃ Order មួយ
    public function orderDetail()
    {
        if (empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/customer/orders');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $db = Database::connection();

        // ទាញយក Order តែពេលវាជារបស់អតិថិជននេះមែន
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
        $stmt->execute([$id, $customerId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            header('Location: ' . BASE_URL . '/customer/orders');
            exit;
        }

        // ទាញយកទំនិញក្នុង Order នេះ
        $stmt = $db->prepare("
            SELECT oi.*, p.name AS product_name, p.image AS product_image 
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$id]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Settings សម្រាប់ Sidebar
        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        $this->view('customer/order-detail', [
            'order' => $order,
            'orderItems' => $orderItems,
            'settingsByKey' => $settingsByKey
        ]);
    }
    // បង្ហាញបញ្ជី Wishlist
    public function wishlist()
    {
        if (empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $db = Database::connection();

        // ទាញយកទំនិញទាំងអស់ក្នុង Wishlist របស់អតិថិជននេះ
        $stmt = $db->prepare("
            SELECT wi.id, wi.created_at, p.id AS product_id, p.name AS product_name, 
                   p.price, p.image AS product_image, p.stock 
            FROM wishlist_items wi
            JOIN wishlists w ON wi.wishlist_id = w.id
            JOIN products p ON wi.product_id = p.id
            WHERE w.user_id = ?
            ORDER BY wi.created_at DESC
        ");
        $stmt->execute([$customerId]);
        $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Settings សម្រាប់ Sidebar
        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        $this->view('customer/wishlist', [
            'wishlistItems' => $wishlistItems,
            'settingsByKey' => $settingsByKey
        ]);
    }

    // លុបទំនិញចេញពី Wishlist
    public function removeFromWishlist()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/wishlist');
            exit;
        }

        $itemId = (int) ($_POST['wishlist_item_id'] ?? 0);
        $customerId = (int) $_SESSION['customer_id'];

        if ($itemId > 0) {
            $db = Database::connection();
            // ពិនិត្យមើលថាតើ Item នេះជារបស់អតិថិជននេះមែនឬអត់ មុននឹងលុប
            $stmt = $db->prepare("
                DELETE wi FROM wishlist_items wi
                JOIN wishlists w ON wi.wishlist_id = w.id
                WHERE wi.id = ? AND w.user_id = ?
            ");
            $stmt->execute([$itemId, $customerId]);
        }

        $_SESSION['success'] = 'Item removed from your wishlist.';
        header('Location: ' . BASE_URL . '/customer/wishlist');
        exit;
    }

    // បង្ហាញបញ្ជី Feedback របស់អតិថិជន
    public function feedbacks()
    {
        if (empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $db = Database::connection();

        // ទាញយក Feedback ទាំងអស់របស់អតិថិជននេះ (ភ្ជាប់ជាមួយឈ្មោះផលិតផល)
        $stmt = $db->prepare("
            SELECT r.*, p.name AS product_name 
            FROM reviews r
            LEFT JOIN products p ON r.product_id = p.id
            WHERE r.customer_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$customerId]);
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Settings សម្រាប់ Sidebar
        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        $this->view('customer/feedback', [
            'feedbacks' => $feedbacks,
            'settingsByKey' => $settingsByKey
        ]);
    }

    // លុប Feedback របស់ខ្លួនឯង
    public function deleteFeedback()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/feedback');
            exit;
        }

        $feedbackId = (int) ($_POST['feedback_id'] ?? 0);
        $customerId = (int) $_SESSION['customer_id'];

        if ($feedbackId > 0) {
            $db = Database::connection();
            // ពិនិត្យមើលថាតើ Feedback នេះជារបស់គាត់មែនឬអត់ មុននឹងលុប
            $stmt = $db->prepare("DELETE FROM reviews WHERE id = ? AND customer_id = ?");
            $stmt->execute([$feedbackId, $customerId]);
        }

        $_SESSION['success'] = 'Feedback deleted successfully.';
        header('Location: ' . BASE_URL . '/customer/feedback');
        exit;
    }
    // បង្ហាញទំព័រ Settings
    public function settings()
    {
        if (empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['customer_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Settings សម្រាប់ Sidebar
        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        $this->view('customer/settings', [
            'user' => $user,
            'settingsByKey' => $settingsByKey
        ]);
    }

    // ធ្វើបច្ចុប្បន្នភាពការកំណត់ (ឧ. ប្តូរលេខសម្ងាត់)
    public function updateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer/settings');
            exit;
        }

        $customerId = (int) $_SESSION['customer_id'];
        $action = $_POST['action'] ?? '';

        $db = Database::connection();

        if ($action === 'change_password') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$customerId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'Current password is incorrect.';
                header('Location: ' . BASE_URL . '/customer/settings');
                exit;
            }

            if (strlen($newPassword) < 6) {
                $_SESSION['error'] = 'New password must be at least 6 characters.';
                header('Location: ' . BASE_URL . '/customer/settings');
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'New passwords do not match.';
                header('Location: ' . BASE_URL . '/customer/settings');
                exit;
            }

            $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $customerId]);

            $_SESSION['success'] = 'Password changed successfully!';
            header('Location: ' . BASE_URL . '/customer/settings');
            exit;
        }

        // ប្រសិនបើចង់បន្ថែម Action ផ្សេងទៀត (ដូចជា លុបគណនី) អាចបន្ថែមនៅទីនេះ
        if ($action === 'delete_account') {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$customerId]);

            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        header('Location: ' . BASE_URL . '/customer/settings');
        exit;
    }
}