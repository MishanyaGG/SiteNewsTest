<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require_admin();
csrf_check();
$id = (int)$_POST['id'];
$stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if($p){
    if($p['image'] && file_exists(UPLOAD_DIR.$p['image'])) @unlink(UPLOAD_DIR.$p['image']);
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
}
header('Location: /admin/');