<?php



// ========== CUSTOMER AUTH ROUTES ==========
$router->get('/customer/login', [CustomerAuthController::class, 'showLogin']);
$router->post('/customer/login', [CustomerAuthController::class, 'login']);
$router->get('/customer/register', [CustomerAuthController::class, 'showRegister']);
$router->post('/customer/register', [CustomerAuthController::class, 'register']);
$router->get('/customer/logout', [CustomerAuthController::class, 'logout']);

// ========== CUSTOMER DASHBOARD ROUTES ==========

$router->get('/', [WelcomeController::class, 'index']);


$router->get('/customer/orders', [CustomerDashboardController::class, 'orders']);
$router->get('/customer/order-detail', [CustomerDashboardController::class, 'orderDetail']);


$router->get('/customer/profile', [WelcomeController::class, 'profile']);
$router->post('/customer/profile/update', [WelcomeController::class, 'updateProfile']);
$router->post('/customer/profile/change-password', [WelcomeController::class, 'changePassword']);

$router->get('/customer/my-orders', [WelcomeController::class, 'orders']);
$router->get('/customer/order-detail', [WelcomeController::class, 'orderDetail']);

$router->get('/customer/wishlist', [WelcomeController::class, 'wishlist']);
$router->post('/customer/wishlist/remove', [WelcomeController::class, 'removeFromWishlist']);


$router->get('/customer/feedbacks', [WelcomeController::class, 'feedbacks']);
$router->post('/customer/feedbacks/delete', [WelcomeController::class, 'deleteFeedback']);


$router->get('/customer/settings', [WelcomeController::class, 'settings']);
$router->post('/customer/settings/update', [WelcomeController::class, 'updateSettings']);

