<?php

require_once __DIR__ . '/../src/system/vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
    'tabouret' => __DIR__ . '/../src/system'
));
$loader->register();
