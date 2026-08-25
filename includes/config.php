<?php
$env = parse_ini_file(__DIR__ . '\..\.env');
// Данные для Beget берутся из панели: «Базы данных» → имя/логин/пароль
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);   // на Beget: логин_имяБД
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']); //x8&7I7VuWE*E

define('SITE_NAME', 'ShopLite');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');