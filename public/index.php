<?php

$baseDir = preg_replace("#/public$#i", "", str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? "")));
define("BASE_URL", $baseDir === "/" ? "" : $baseDir);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ប្រើ dirname(__DIR__) សម្រាប់គ្រប់ File ទាំងអស់ ដើម្បីចេញពី Folder public ទៅរក Folder app
require_once dirname(__DIR__) . '/app/Core/Database.php';
require_once dirname(__DIR__) . '/app/Core/Controller.php';
require_once dirname(__DIR__) . '/app/Core/Routes.php';

// Controllers (ត្រូវប្រើ dirname(__DIR__) ដូចគ្នា)
require_once dirname(__DIR__) . '/app/Controller/WelcomeController.php';
require_once dirname(__DIR__) . '/app/Controller/CustomerAuthController.php';
require_once dirname(__DIR__) . '/app/Controller/CustomerDashboardController.php';

// Models (ប្រសិនបើអ្នកមាន Model មូលដ្ឋាន)
require_once dirname(__DIR__) . '/app/Models/Model.php';
// បន្ថែមបន្ទាត់នេះ
require_once dirname(__DIR__) . '/app/Models/Setting.php';

// ប្រសិនបើអ្នកមាន Model ផ្សេងៗទៀត សូមបន្ថែមនៅទីនេះ (ឬប្រើ Autoloader)
// require_once dirname(__DIR__) . '/app/Models/Order.php';
// require_once dirname(__DIR__) . '/app/Models/Setting.php';
// ...

$router = new Router();

// Require file web.php (ឬ Routes.php របស់អ្នក)
require_once dirname(__DIR__) . '/routes/web.php';

// ដំណើរការ Router
$router->dispatch();