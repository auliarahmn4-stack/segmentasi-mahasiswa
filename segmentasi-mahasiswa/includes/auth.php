<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__.'/../config/app.php';
require_once __DIR__.'/functions.php';
$publicPages = ['login'];
$page = $_GET['page'] ?? 'dashboard';
if (!in_array($page,$publicPages,true) && empty($_SESSION['user_id'])) {
    header('Location: '.APP_BASE.'/auth/login.php'); exit;
}
