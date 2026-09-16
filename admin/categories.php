<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require_admin();

// Получаем категории вместе с количеством товаров
$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) AS products_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
")->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Категории</h2>
    <a href="category_form.php" class="btn btn-success">+ Добавить категорию</a>
  </div>

  <?php if(!empty($_GET['deleted'])): ?>
    <div class="alert alert-success">Категория удалена</div>
  <?php endif; ?>
  <?php if(!empty($_GET['saved'])): ?>
    <div class="alert alert-success">Изменения сохранены</div>
  <?php endif; ?>
  <?php if(!empty($_GET['error'])): ?>
    <div class="alert alert-danger">
      <?= e($_GET['error'] === 'has_products'
            ? 'Нельзя удалить категорию: в ней есть товары. Сначала удалите или перенесите их.'
            : 'Произошла ошибка') ?>
    </div>
  <?php endif; ?>

  <!-- Локальная навигация админки -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" href="/admin/">Товары</a></li>
    <li class="nav-item"><a class="nav-link" href="/admin/categories.php">Категории</a></li>
  </ul>

  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Slug (URL)</th>
        <th>Товаров</th>
        <th class="text-end">Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!$categories): ?>
        <tr><td colspan="5" class="text-center text-muted">Категорий пока нет</td></tr>
      <?php endif; ?>
      <?php foreach($categories as $c): ?>
        <tr>
          <td><?=$c['id']?></td>
          <td><b><?=e($c['name'])?></b></td>
          <td><code><?=e($c['slug'])?></code></td>
          <td>
            <span class="badge bg-secondary"><?=$c['products_count']?></span>
          </td>
          <td class="text-end">
            <a href="category_form.php?id=<?=$c['id']?>" class="btn btn-sm btn-primary">Ред.</a>
            <form method="post" action="category_delete.php" class="d-inline"
                  onsubmit="return confirm('Удалить категорию <?=e($c['name'])?>')">
              <input type="hidden" name="csrf" value="<?=csrf_token()?>">
              <input type="hidden" name="id" value="<?=$c['id']?>">
              <button class="btn btn-sm btn-danger">Удалить</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>