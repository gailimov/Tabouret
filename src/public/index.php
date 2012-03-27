<?php

require_once __DIR__ . '/../system/vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
    'tabouret' => __DIR__ . '/../system'
));
$loader->register();

\tabouret\Registry::set('rootPath', __DIR__ . '/../');

\tabouret\Router::getInstance()
    ->add('home', array('^$', 'main.site.index'))
    ->add('post', array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show'))
    ->dispatch();
