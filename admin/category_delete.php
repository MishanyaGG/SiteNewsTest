<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require_admin();
csrf_check();

$id = (int)($_POST['id'] ?? 0);

try {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$id]);
    header('Location: /admin/categories.php?deleted=1');
    exit;
} catch (PDOException $e) {
    // SQLSTATE 23000 — нарушение целостности (есть связанные товары)
    if($e->getCode() == 23000){
        header('Location: /admin/categories.php?error=has_products');
        exit;
    }
    throw $e;
}