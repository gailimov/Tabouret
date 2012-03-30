# Tabouret — Lightweight PHP Framework

"Simple as a tabouret". Its goal is establish peace throughout the world and cure cancer.

### Still under the development.

## Usage examples

### First set routes (app/config/app.php).

Routes includes name and rule. Rule consist of pattern and action. Action is specified in format: "module.controller.action". Yes, it similar to Django :).

```php
<?php

return array(
    // Path to application
    'appPath' => __DIR__ . '/..',
    // Set the action that will be handle 404 error
    'error404Action' => 'main.site.error404',

    'routes' => array(
        'home' => array('^$', 'blog.posts.index'),
        'post' => array('^posts/(?P<slug>[-_a-z0-9]+)$', 'blog.posts.show')
    )
);
```

### Then create module and controller (app/modules/blog/controllers/posts.php).

Controller is just file with functions (actions). Its namespace consist of the name of module and controller. To access the params use $_GET.

```php
<?php

namespace blog\posts;

use tabouret\View;

$postsModel = new PostsModel();

function index()
{
    $posts = $postsModel->getAll();
    View::render(array('posts' => $posts));
}

function show()
{
    $post = $postsModel->getBySlug($_GET['slug']);
    $commentsModel = new CommentsModel();
    $comments = $commentsModel->getByPost($post->id);
    View::render(array('post' => $post, 'comments' => $comments));
}
```

### Layout (app/views/layouts/app.php):

```php
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Blog</title>
    </head>
    <body>
        <?= $content ?>
    </body>
</html>
```

### Views (app/modules/blog/views/posts/):

```php
<!-- index.php -->
<?php foreach ($posts as $post): ?>
    <h1><a href="<?= App::getInstance()->createUrl('post', array('slug' => $post->slug)) ?>"><?= $post->title ?></a></h1>
    <?= $post->content ?>
<?php endforeach ?>
```

```php
<!-- show.php -->
<h1><?= $post->title ?></h1>
<?= $post->content ?>
<h2>Comments:</h2>
<?php View::renderPartial('posts/_comments', array('comments' => $comments)) ?>
```

```php
<!-- _comments.php -->
<?php foreach ($comments as $comment): ?>
    <?= $comment->author ?>
    <?= $comment->content ?>
<?php endforeach ?>
```

### OK, where is the model?

Framework provides full freedom to you. You can use Zend_Db or Doctrine or %your_favourite_orm%.

## License

For the license information, please view the LICENSE file that was distributed with this source code.

## Enjoy ;)
