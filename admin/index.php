<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require_admin();
$products = $pdo->query("SELECT p.*, c.name AS cat_name FROM products p
                         JOIN categories c ON c.id=p.category_id
                         ORDER BY p.id DESC")->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h2>Товары</h2>
    <a href="add.php" class="btn btn-success">+ Добавить товар</a>
  </div>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Фото</th><th>Название</th><th>Категория</th>
    <th>Цена</th><th>Остаток</th><th></th></tr></thead>
    <tbody>
    <?php foreach($products as $p): ?>
      <tr>
        <td><?=$p['id']?></td>
        <td><img src="/uploads/<?=e($p['image'] ?: 'noimage.png')?>" width="50"></td>
        <td><?=e($p['name'])?></td>
        <td><?=e($p['cat_name'])?></td>
        <td><?=number_format($p['price'],0,',',' ')?> ₽</td>
        <td><?=$p['stock']?></td>
        <td>
          <a href="edit.php?id=<?=$p['id']?>" class="btn btn-sm btn-primary">Изменить</a>
          <form method="post" action="delete.php" class="d-inline"
                onsubmit="return confirm('Удалить?')">
            <input type="hidden" name="csrf" value="<?=csrf_token()?>">
            <input type="hidden" name="id" value="<?=$p['id']?>">
            <button class="btn btn-sm btn-danger">Удалить</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>