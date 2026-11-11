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
    <title>My Orders · Customer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
        }

        /* Table */
        .table-wrapper {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border: 1px solid #f0f2f9;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h3 {
            font-size: 18px;
            color: #1e1e2f;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f9faff;
            color: #7b8499;
            padding: 14px 16px;
            border-bottom: 2px solid #eef1f7;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f4f6fb;
            color: #2d3648;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafbff;
        }

        /* Badges */
        .badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
        }

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-confirmed {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-shipping {
            background: #ede9fe;
            color: #8b5cf6;
        }

        .badge-completed {
            background: #d1fae5;
            color: #10b981;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #ef4444;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #ef4444;
        }

        .badge-paid {
            background: #d1fae5;
            color: #10b981;
        }

        .badge-refunded {
            background: #fef3c7;
            color: #d97706;
        }

        .btn-view {
            padding: 6px 12px;
            background: #f1f5f9;
            color: #3d4a5e;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-view:hover {
            background: #e2e8f0;
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
        
            <!-- បង្ហាញឈ្មោះអ្នកប្រើ -->
            <div class="logo-text" style="font-size: 18px; line-height: 1.2;">
                <?= htmlspecialchars($_SESSION['customer_name'] ?? 'Customer') ?>
                <div style="font-size: 12px; color: rgba(255,255,255,0.7); font-weight: 400;">Customer</div>
            </div>
        </div>

       <nav class="nav">

                <a href="<?= BASE_URL ?>/" class="nav-item ">
            <i class="fas fa-chart-pie"></i>
            <span>My Dashboard</span>
        </a>
    
        <a href="<?= BASE_URL ?>/customer/profile" class="nav-item ">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
        </a>
    
        <a href="<?= BASE_URL ?>/customer/my-orders" class="nav-item active">
            <i class="fas fa-shopping-bag"></i>
            <span>My Orders</span>
        </a>
    
        <a href="<?= BASE_URL ?>/customer/wishlist" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>My Wishlist</span>
        </a>
    
        <a href="<?= BASE_URL ?>/customer/my-feedback" class="nav-item">
            <i class="fas fa-comments"></i>
            <span>My Feedback</span>
        </a>
    
        <!-- <a href="<?= BASE_URL ?>/customer/my-addresses" class="nav-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>My Addresses</span>
        </a> -->
    
        <a href="<?= BASE_URL ?>/customer/settings" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    
    </nav>

        <div class="bottom-nav">
            <a href="<?= BASE_URL ?>/customer/logout" class="logout-item"><i class="fas fa-sign-out-alt"></i>
                <span>Logout</span></a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">My Account <span>/ My Orders</span></div>
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
            <div class="table-wrapper">
                <div class="table-header">
                    <h3><i class="fas fa-shopping-bag" style="color: #8200DC;"></i> Order History</h3>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                        <i class="fas fa-box-open"
                                            style="font-size: 32px; margin-bottom: 12px; display: block; color: #cbd5e1;"></i>
                                        You haven't placed any orders yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: #8200DC;">
                                            #
                                            <?= e($order['order_number']) ?>
                                        </td>
                                        <td style="color: #64748b;">
                                            <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                        </td>
                                        <td style="font-weight: 600;">
                                            $
                                            <?= number_format((float) $order['final_amount'], 2) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $order['status'] ?? 'pending';
                                            $badgeClass = 'badge-' . $status;
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= e($status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $payStatus = $order['payment_status'] ?? 'unpaid';
                                            $payBadgeClass = 'badge-' . $payStatus;
                                            ?>
                                            <span class="badge <?= $payBadgeClass ?>">
                                                <?= e($payStatus) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/customer/order-detail?id=<?= (int) $order['id'] ?>"
                                                class="btn-view">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>