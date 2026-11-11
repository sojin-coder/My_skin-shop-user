<?php
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register · Skincare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: #8200DC;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-box {
            background: #fff;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3);
        }

        .logo-area {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: #8200DC;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin: 0 auto 12px;
        }

        .register-title h2 {
            color: #1e293b;
            font-size: 20px;
            text-align: center;
            margin-bottom: 24px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .form-group {
            margin-bottom: 16px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #3d4a5e;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 15px 13px 40px;
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

        .form-group i {
            position: absolute;
            left: 14px;
            top: 40px;
            color: #94a3b8;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: #8200DC;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: #6a01b6;
        }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #64748b;
        }

        .footer-link a {
            color: #8200DC;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="register-box">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-user-plus"></i></div>
            <h2>Create Account</h2>
        </div>

        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-error">
                <ul style="margin-left: 16px;">
                    <?php foreach ($_SESSION['errors'] as $err): ?>
                        <li>
                            <?= htmlspecialchars($err) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/customer/register">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                    placeholder="Your full name" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                    placeholder="your@email.com" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="At least 6 characters" required>
                <i class="fas fa-lock"></i>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="btn-register">Register <i class="fas fa-arrow-right"></i></button>
        </form>

        <div class="footer-link">
            Already have an account? <a href="<?= BASE_URL ?>/customer/login">Login here</a>
        </div>
    </div>
</body>

</html>