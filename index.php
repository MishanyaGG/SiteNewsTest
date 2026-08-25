<?php
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$q        = trim($_GET['q'] ?? '');
$cat      = (int)($_GET['cat'] ?? 0);
$min      = (float)($_GET['min'] ?? 0);
$max      = (float)($_GET['max'] ?? 0);
$sort     = $_GET['sort'] ?? 'new';

$sql = "SELECT p.*, c.name AS cat_name FROM products p
        JOIN categories c ON c.id=p.category_id WHERE 1=1";
$params = [];

if($q !== ''){
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$q%"; $params[] = "%$q%";
}
if($cat > 0){ $sql .= " AND p.category_id=?"; $params[] = $cat; }
if($min > 0){ $sql .= " AND p.price>=?"; $params[] = $min; }
if($max > 0){ $sql .= " AND p.price<=?"; $params[] = $max; }

$sql .= match($sort){
    'price_asc'  => " ORDER BY p.price ASC",
    'price_desc' => " ORDER BY p.price DESC",
    default      => " ORDER BY p.created_at DESC",
};

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
$categories = get_categories($pdo);

require __DIR__ . '/includes/header.php';
?>
<div class="container py-4">
  <h1 class="mb-4">Каталог товаров</h1>

  <form method="get" class="row g-2 mb-4">
    <div class="col-md-4">
      <input type="text" name="q" class="form-control" placeholder="Поиск..." value="<?=e($q)?>">
    </div>
    <div class="col-md-3">
      <select name="cat" class="form-select">
        <option value="0">Все категории</option>
        <?php foreach($categories as $c): ?>
          <option value="<?=$c['id']?>" <?=$cat==$c['id']?'selected':''?>>
            <?=e($c['name'])?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-1"><input type="number" name="min" class="form-control" placeholder="от" value="<?=$min?:''?>"></div>
    <div class="col-md-1"><input type="number" name="max" class="form-control" placeholder="до" value="<?=$max?:''?>"></div>
    <div class="col-md-2">
      <select name="sort" class="form-select">
        <option value="new" <?=$sort==='new'?'selected':''?>>Новые</option>
        <option value="price_asc" <?=$sort==='price_asc'?'selected':''?>>Дешевле</option>
        <option value="price_desc" <?=$sort==='price_desc'?'selected':''?>>Дороже</option>
      </select>
    </div>
    <div class="col-md-1"><button class="btn btn-primary w-100">Найти</button></div>
  </form>

  <div class="row">
    <?php if(!$products): ?>
      <div class="col-12"><div class="alert alert-info">Ничего не найдено.</div></div>
    <?php endif; ?>
    <?php foreach($products as $p): ?>
      <div class="col-md-3 mb-4">
        <div class="card h-100">
          <img src="/uploads/<?=e($p['image'] ?: 'noimage.png')?>" class="card-img-top" alt="">
          <div class="card-body">
            <h5 class="card-title"><?=e($p['name'])?></h5>
            <small class="text-muted"><?=e($p['cat_name'])?></small>
            <p class="card-text mt-2"><b><?=number_format($p['price'],0,',',' ')?>&nbsp;₽</b></p>
            <a href="/product.php?id=<?=$p['id']?>" class="btn btn-outline-primary btn-sm">Подробнее</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>