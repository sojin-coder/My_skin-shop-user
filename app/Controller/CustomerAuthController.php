<?php

class CustomerAuthController extends Controller
{
    // បង្ហាញ Form Login
    public function showLogin()
    {
        // ប្រសិនបើ Login រួចហើយ ឱ្យទៅ Dashboard
        if (!empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        $this->view('auth/customer-login');
    }

    // ដំណើរការ Login
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Please enter email and password.';
            header('Location: ' . BASE_URL . '/customer/login');
            exit;
        }

        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // បង្កើត Session សម្រាប់អតិថិជន
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_name'] = $user['name'];
            $_SESSION['customer_email'] = $user['email'];

            header('Location: ' . BASE_URL . '/');
            exit;
        }

        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: ' . BASE_URL . '/customer/login');
        exit;
    }

    // បង្ហាញ Form Register
    public function showRegister()
    {
        if (!empty($_SESSION['customer_id'])) {
            header('Location: ' . BASE_URL . '/customer');
            exit;
        }
        $this->view('auth/customer-register');
    }

    // ដំណើរការ Register
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/customer/register');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $errors = [];

        if ($name === '')
            $errors[] = 'Name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'Valid email is required.';
        if (strlen($password) < 6)
            $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm)
            $errors[] = 'Passwords do not match.';

        // ពិនិត្យមើលថាតើ Email ស្ទួនឬអត់
        if (empty($errors)) {
            $db = Database::connection();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email already exists.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            header('Location: ' . BASE_URL . '/customer/register');
            exit;
        }

        // បញ្ចូលអតិថិជនថ្មីចូល Database
        $db = Database::connection();
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role, status, created_at, updated_at) 
            VALUES (?, ?, ?, 'customer', 1, NOW(), NOW())
        ");
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

        $_SESSION['success'] = 'Account created! Please login.';
        header('Location: ' . BASE_URL . '/customer/login');
        exit;
    }

    // Logout
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/customer/login');
        exit;
    }
}