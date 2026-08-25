<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
$error = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? AND role='admin'");
    $stmt->execute([$_POST['username'] ?? '']);
    $u = $stmt->fetch();
    if($u && password_verify($_POST['password'] ?? '', $u['password_hash'])){
        $_SESSION['user'] = ['id'=>$u['id'],'username'=>$u['username'],'role'=>$u['role']];
        header('Location: /admin/'); exit;
    }
    $error = 'Неверный логин или пароль';
}
?>
<!doctype html><html><head><meta charset="utf-8">
<title>Вход</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container" style="max-width:400px;margin-top:80px">
  <div class="card p-4">
    <h3 class="mb-3">Вход в админ-панель</h3>
    <?php if($error): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?=csrf_token()?>">
      <input name="username" class="form-control mb-2" placeholder="Логин" required>
      <input name="password" type="password" class="form-control mb-3" placeholder="Пароль" required>
      <button class="btn btn-primary w-100">Войти</button>
    </form>
    <a href="/"><button style="margin-top: 10px" class="btn btn-danger w-100">Главная страница</button></a> 
  </div>
</div></body></html>