<?php
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
$settingsByKey = $settingsByKey ?? [];
function getSettingValue(array $settings, string $key): string
{
    $val = $settings[$key] ?? '';
    if (is_array($val))
        return (string) ($val['setting_value'] ?? '');
    return (string) $val;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings · Customer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ========== CSS ដូចដើម ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f4f6fb;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        a {
            text-decoration: none;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #8200DC;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 28px 16px;
            flex-shrink: 0;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 12px 24px 12px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 24px;
        }

        .logo-icon {
            background: #d92afc;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            font-weight: 700;
        }

        .nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            font-size: 15px;
            transition: 0.2s;
        }

        .nav-item i {
            width: 24px;
            text-align: center;
            font-size: 18px;
        }

        .nav-item.active {
            background: #9b0eff;
            font-weight: 600;
        }

        .nav-item:hover:not(.active) {
            background: rgba(255, 255, 255, 0.15);
        }

        .bottom-nav {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .logout-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border-radius: 12px;
            color: #f31717;
            background: #eee;
            font-weight: 600;
            font-size: 15px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 32px;
            background: white;
            border-bottom: 1px solid #eef1f7;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e1e2f;
        }

        .page-title span {
            font-size: 14px;
            color: #7b8499;
            font-weight: 400;
            margin-left: 8px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .content-area {
            flex: 1;
            padding: 28px 32px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
            align-items: center;
        }

        /* Settings Card */
        .settings-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f2f9;
        }

        .settings-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f4f6fb;
        }

        .settings-header h3 {
            font-size: 20px;
            color: #1e1e2f;
        }

        .settings-header i {
            font-size: 24px;
            color: #8200DC;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #3d4a5e;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            background: #f8fafc;
            outline: none;
            transition: 0.2s;
        }

        .form-group input:focus {
            border-color: #8200DC;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(130, 0, 220, 0.1);
        }

        .btn-save {
            padding: 12px 24px;
            background: #8200DC;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-save:hover {
            background: #6a01b6;
            transform: translateY(-2px);
        }

        .btn-danger {
            padding: 12px 24px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            max-width: 700px;
        }

        .alert-success {
            background: #d1fae5;
            color: #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #ef4444;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon" style="background: #fff; color: #8200DC; font-weight: 700; font-size: 18px;">
                <?= strtoupper(substr($_SESSION['customer_name'] ?? 'C', 0, 1)) ?>
            </div>
            <div class="logo-text" style="font-size: 18px; line-height: 1.2;">
                <?= htmlspecialchars($_SESSION['customer_name'] ?? 'Customer') ?>
                <div style="font-size: 12px; color: rgba(255,255,255,0.7); font-weight: 400;">Customer</div>
            </div>
        </div>

        <nav class="nav">
            <a href="<?= BASE_URL ?>/" class="nav-item"><i class="fas fa-chart-pie"></i> <span>My
                    Dashboard</span></a>
            <a href="<?= BASE_URL ?>/customer/profile" class="nav-item"><i class="fas fa-user"></i> <span>My
                    Profile</span></a>
            <a href="<?= BASE_URL ?>/customer/orders" class="nav-item"><i class="fas fa-shopping-bag"></i> <span>My
                    Orders</span></a>
            <a href="<?= BASE_URL ?>/customer/wishlist" class="nav-item"><i class="fas fa-heart"></i> <span>My
                    Wishlist</span></a>
            <a href="<?= BASE_URL ?>/customer/feedback" class="nav-item"><i class="fas fa-comments"></i> <span>My
                    Feedback</span></a>
            <!-- <a href="<?= BASE_URL ?>/customer/addresses" class="nav-item"><i class="fas fa-map-marker-alt"></i> <span>My
                    Addresses</span></a> -->
            <!-- ដាក់ active ត្រង់ Settings -->
            <a href="<?= BASE_URL ?>/customer/settings" class="nav-item active"><i class="fas fa-cog"></i>
                <span>Settings</span></a>
        </nav>

        <div class="bottom-nav">
            <a href="<?= BASE_URL ?>/customer/logout" class="logout-item"><i class="fas fa-sign-out-alt"></i>
                <span>Logout</span></a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">My Account <span>/ Settings</span></div>
            <div class="user-profile">
                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['customer_name'] ?? 'C', 0, 1)) ?>
                </div>
                <div style="font-weight: 600; font-size: 14px;">
                    <?= e($_SESSION['customer_name'] ?? 'Customer') ?>
                </div>
            </div>
        </div>

        <div class="content-area">

            <!-- Alerts -->
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Change Password Section -->
            <div class="settings-card">
                <div class="settings-header">
                    <i class="fas fa-lock"></i>
                    <h3>Change Password</h3>
                </div>

                <form action="<?= BASE_URL ?>/customer/settings/update" method="POST">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" placeholder="At least 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                    <button type="submit" class="btn-save"><i class="fas fa-key"></i> Update Password</button>
                </form>
            </div>

            <!-- Danger Zone (Delete Account) -->
            <div class="settings-card" style="border-left: 4px solid #ef4444;">
                <div class="settings-header" style="border-bottom-color: #fee2e2;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                    <h3 style="color: #ef4444;">Danger Zone</h3>
                </div>
                <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">
                    Once you delete your account, there is no going back. Please be certain.
                </p>
                <form action="<?= BASE_URL ?>/customer/settings/update" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone!');">
                    <input type="hidden" name="action" value="delete_account">
                    <button type="submit" class="btn-danger"><i class="fas fa-trash"></i> Delete My Account</button>
                </form>
            </div>

        </div>
    </main>
</body>

</html>