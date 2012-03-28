<?php

return array(
    'appPath' => __DIR__ . '/../src/app',

    'routes' => array(
        'home' => array('^$', 'main.site.index'),
        'post' => array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show')
    )
);
