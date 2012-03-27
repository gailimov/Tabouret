<?php

use tabouret\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testGetInstance()
    {
        $obj1 = Router::getInstance();
        $obj2 = Router::getInstance();
        $this->assertTrue($obj1 === $obj2);
    }

    public function testCreateUrl()
    {
        $router = Router::getInstance();
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['SCRIPT_NAME'] = '/';
        $router->add('home', array('^$', 'main.site.index'))
               ->add('post', array('^posts/(?P<slug>[-_a-z0-9]+)$', 'blog.posts.show'));
        $this->assertEquals('/', $router->createUrl('home'));
        $this->assertEquals('/posts/something', $router->createUrl('post', array('slug' => 'something')));
        $this->assertEquals('https://localhost/posts/something',
                             $router->createUrl('post', array('slug' => 'something'), true, true));
    }
}
