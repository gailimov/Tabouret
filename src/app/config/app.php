<?php

return array(
    //'appPath' => __DIR__ . '/..',
    'error404Action' => 'main.site.error404',

    'routes' => array(
        'home' => array('^$', 'main.site.index'),
        'posts' => array('^posts$', 'blog.posts.index'),
        'post' => array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show')
    )
);
