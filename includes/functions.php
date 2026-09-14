<?php
session_start();

function transliterate(string $text): string {
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh',
        'з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o',
        'п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts',
        'ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        ' '=>'-'
    ];
    $text = mb_strtolower($text);
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9\-]/', '', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

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