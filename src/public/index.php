<?php

error_reporting(E_ALL | E_STRICT);

// Change the following paths if necessary
$app = __DIR__ . '/../system/tabouret/App.php';
$config = __DIR__ . '/../app/config/app.php';

require_once $app;

tabouret\App::getInstance()->init($config)->run();
