<?php
session_start();

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

function csrf_token(){
    if(empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check(){
    if(!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? ''))
        die('Ошибка CSRF-токена');
}

function require_admin(){
    if(empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: /admin/login.php'); exit;
    }
}

function get_categories($pdo){
    return $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
}