<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, c.name AS cat_name FROM products p
                       JOIN categories c ON c.id=p.category_id WHERE p.id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if(!$p){ http_response_code(404); die('Товар не найден'); }
require __DIR__ . '/includes/header.php';
?>
<div class="container py-4">
  <a href="/" class="btn btn-link">← В каталог</a>
  <div class="row">
    <div class="col-md-5">
      <img src="/uploads/<?=e($p['image'] ?: 'noimage.png')?>" class="img-fluid rounded">
    </div>
    <div class="col-md-7">
      <h1><?=e($p['name'])?></h1>
      <p class="text-muted">Категория: <?=e($p['cat_name'])?></p>
      <h3 class="text-primary"><?=number_format($p['price'],0,',',' ')?>&nbsp;₽</h3>
      <p><?=nl2br(e($p['description']))?></p>
      <p>В наличии: <b><?=$p['stock']?></b> шт.</p>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>