<?php
session_start();require_once __DIR__.'/../config/app.php';if(empty($_SESSION['user_id'])){header('Location: '.APP_BASE.'/auth/login.php');exit;}
$run=(int)($_GET['run']??0);header('Location: '.APP_BASE.'/index.php?page=laporan&run='.$run);exit;
