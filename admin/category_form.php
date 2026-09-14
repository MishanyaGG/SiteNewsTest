<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$category = ['name' => '', 'slug' => ''];

if($isEdit){
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$id]);
    $category = $stmt->fetch() ?: die('Категория не найдена');
}

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    csrf_check();

    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    // Автогенерация slug, если не указан
    if($slug === ''){
        $slug = transliterate($name);
    }
    $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($slug));

    if($name === '') $errors[] = 'Введите название категории';
    if($slug === '') $errors[] = 'Не удалось сформировать slug';

    // Проверка уникальности slug
    if(!$errors){
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug=? AND id<>?");
        $stmt->execute([$slug, $id]);
        if($stmt->fetch()){
            $errors[] = 'Slug "'.$slug.'" уже используется';
        }
    }

    if(!$errors){
        if($isEdit){
            $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=? WHERE id=?");
            $stmt->execute([$name, $slug, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
            $stmt->execute([$name, $slug]);
        }
        header('Location: /admin/categories.php?saved=1');
        exit;
    }

    $category = ['name' => $name, 'slug' => $slug];
}

require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h2><?=$isEdit ? 'Редактировать категорию' : 'Новая категория'?></h2>

  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link" href="/admin/">Товары</a></li>
    <li class="nav-item"><a class="nav-link active" href="/admin/categories.php">Категории</a></li>
    <li class="nav-item ms-auto"><a class="nav-link text-danger" href="/admin/logout.php">Выйти</a></li>
  </ul>

  <?php foreach($errors as $er): ?>
    <div class="alert alert-danger"><?=e($er)?></div>
  <?php endforeach; ?>

  <form method="post" class="card p-3" style="max-width:600px">
    <input type="hidden" name="csrf" value="<?=csrf_token()?>">

    <div class="mb-3">
      <label class="form-label">Название</label>
      <input name="name" class="form-control" value="<?=e($category['name'])?>" required
             placeholder="Например: Электроника">
    </div>

    <div class="mb-3">
      <label class="form-label">Slug (URL-имя)</label>
      <input name="slug" class="form-control" value="<?=e($category['slug'])?>"
             placeholder="electronics (можно оставить пустым — сгенерируется автоматически)">
      <small class="text-muted">Только латиница, цифры и дефис. Если пусто — создастся из названия.</small>
    </div>

    <button class="btn btn-primary">Сохранить</button>
    <a href="/admin/categories.php" class="btn btn-secondary">Отмена</a>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>