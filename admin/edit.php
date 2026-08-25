<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$product = ['category_id'=>0,'name'=>'','description'=>'','price'=>0,'stock'=>0,'image'=>''];

if($isEdit){
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $product = $stmt->fetch() ?: die('Товар не найден');
}

$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $product = [
        'category_id'=>(int)$_POST['category_id'],
        'name'=>trim($_POST['name']),
        'description'=>trim($_POST['description']),
        'price'=>(float)$_POST['price'],
        'stock'=>(int)$_POST['stock'],
        'image'=>$product['image'],
    ];
    if($product['name']==='') $errors[]='Введите название';
    if($product['price']<=0)  $errors[]='Цена должна быть > 0';

    // загрузка картинки
    if(!empty($_FILES['image']['name'])){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(!in_array($ext,['jpg','jpeg','png','webp'])) $errors[]='Неверный тип файла';
        else {
            $newName = uniqid('p_').'.'.$ext;
            if(move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR.$newName)){
                $product['image'] = $newName;
            }
        }
    }

    if(!$errors){
        if($isEdit){
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?,
                                   price=?, stock=?, image=? WHERE id=?");
            $stmt->execute([$product['category_id'],$product['name'],$product['description'],
                            $product['price'],$product['stock'],$product['image'],$id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id,name,description,price,stock,image)
                                   VALUES (?,?,?,?,?,?)");
            $stmt->execute([$product['category_id'],$product['name'],$product['description'],
                            $product['price'],$product['stock'],$product['image']]);
        }
        header('Location: /admin/'); exit;
    }
}
$categories = get_categories($pdo);
require __DIR__ . '/../includes/header.php';
?>
<div class="container py-4">
  <h2><?=$isEdit?'Редактировать':'Добавить'?> товар</h2>
  <?php foreach($errors as $er): ?><div class="alert alert-danger"><?=e($er)?></div><?php endforeach; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?=csrf_token()?>">
    <div class="mb-2">
      <label>Категория</label>
      <select name="category_id" class="form-select" required>
        <?php foreach($categories as $c): ?>
          <option value="<?=$c['id']?>" <?=$product['category_id']==$c['id']?'selected':''?>>
            <?=e($c['name'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-2"><label>Название</label>
      <input name="name" class="form-control" value="<?=e($product['name'])?>" required></div>
    <div class="mb-2"><label>Описание</label>
      <textarea name="description" class="form-control" rows="4"><?=e($product['description'])?></textarea></div>
    <div class="row">
      <div class="col"><label>Цена</label>
        <input name="price" type="number" step="0.01" class="form-control"
               value="<?=$product['price']?>" required></div>
      <div class="col"><label>Остаток</label>
        <input name="stock" type="number" class="form-control" value="<?=$product['stock']?>"></div>
    </div>
    <div class="mb-2 mt-2"><label>Картинка</label>
      <input name="image" type="file" class="form-control" accept="image/*">
      <?php if($product['image']): ?>
        <small>Текущая: <?=e($product['image'])?></small>
      <?php endif; ?>
    </div>
    <button class="btn btn-primary mt-2">Сохранить</button>
    <a href="/admin/" class="btn btn-secondary mt-2">Отмена</a>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>