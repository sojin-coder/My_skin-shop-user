<?php

class CustomerDashboardController extends Controller
{
    // ពិនិត្យ Login មុននឹងចូលប្រើប្រាស់
    public function __construct()
    {
        if (empty($_SESSION['customer_id'])) {
            $_SESSION['error'] = 'Please login to access your dashboard.';
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }
    }

    // ១. ទំព័រ Overview (Dashboard)
    public function index()
    {
        $customerId = (int) $_SESSION['customer_id'];
        $db = Database::connection();

        // --- ទាញយកចំនួន Order សរុប ---
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM orders WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $totalOrders = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // --- ទាញយកចំនួនទំនិញក្នុង Wishlist ---
        // ដោយសារ Wishlist មានតារាង wishlist_items ដាច់ដោយឡែក
        $stmt = $db->prepare("
            SELECT COUNT(wi.id) AS total 
            FROM wishlist_items wi
            JOIN wishlists w ON wi.wishlist_id = w.id
            WHERE w.user_id = ?
        ");
        $stmt->execute([$customerId]);
        $wishlistCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // --- ទាញយកចំនួន Feedback ដែលគាត់បានសរសេរ ---
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM reviews WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        $feedbackCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // --- ទាញយក Order ចុងក្រោយបំផុត ៥ ---
        $stmt = $db->prepare("
            SELECT * FROM orders 
            WHERE customer_id = ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute([$customerId]);
        $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- ទាញយក Settings សម្រាប់ Sidebar (Logo & Name) ---
        $settingModel = new Setting();
        $settings = $settingModel->all();
        $settingsByKey = [];
        foreach ($settings as $item) {
            $settingsByKey[$item['setting_key']] = $item;
        }

        // --- បញ្ជូនទិន្នន័យទាំងអស់ទៅ View ---
        $this->view('customer/dashboard', [
            'totalOrders' => $totalOrders,
            'wishlistCount' => $wishlistCount,
            'feedbackCount' => $feedbackCount,
            'recentOrders' => $recentOrders,
            'settingsByKey' => $settingsByKey
        ]);
    }
}