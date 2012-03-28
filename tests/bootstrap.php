<?php

error_reporting(E_ALL | E_STRICT);

// Change the following paths if necessary
$app = __DIR__ . '/../src/system/tabouret/App.php';
$config = __DIR__ . '/config.php';

require_once $app;

tabouret\App::getInstance()->init($config);
