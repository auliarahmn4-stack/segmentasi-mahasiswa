<?php
session_start();
require_once __DIR__.'/../config/app.php'; require_once __DIR__.'/../includes/functions.php'; require_once __DIR__.'/../config/database.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: '.APP_BASE.'/auth/login.php');exit;}
verify_csrf();
$identity=trim((string)($_POST['identity']??'')); $password=(string)($_POST['password']??'');
if($identity===''||$password===''){$_SESSION['login_error']='Isi username/email dan password.';header('Location: '.APP_BASE.'/auth/login.php');exit;}
$stmt=$pdo->prepare('SELECT id,nama,username,email,password,role FROM users WHERE username=? OR email=? LIMIT 1');$stmt->execute([$identity,$identity]);$user=$stmt->fetch();
if(!$user||!password_verify($password,$user['password'])){$_SESSION['login_error']='Username/email atau password tidak sesuai.';header('Location: '.APP_BASE.'/auth/login.php');exit;}
session_regenerate_id(true);$_SESSION['user_id']=(int)$user['id'];$_SESSION['nama']=$user['nama'];$_SESSION['role']=$user['role'];log_activity($pdo,'LOGIN','Login berhasil');header('Location: '.APP_BASE.'/index.php');exit;
