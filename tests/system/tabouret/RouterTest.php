<?php

use tabouret\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
    private $_router;

    private $_routes = array(
        'home' => array('^$', 'main.site.index'),
        'post' => array('^posts/(?P<slug>[-_a-z0-9]+)$', 'blog.posts.show')
    );

    public function setUp()
    {
        $this->_router = new Router();
        $this->_router->addRoutes($this->_routes);
    }

    public function testCreateUrl()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['SCRIPT_NAME'] = '/';
        $this->assertEquals('/', $this->_router->createUrl('home'));
        $this->assertEquals('/posts/something', $this->_router->createUrl('post', array('slug' => 'something')));
        $this->assertEquals('https://localhost/posts/something',
                             $this->_router->createUrl('post', array('slug' => 'something'), true, true));
    }

    public function testIsMatched()
    {
        $this->assertTrue($this->_router->isMatched('posts/something'));
        $this->assertFalse($this->_router->isMatched('posts/foo/bar'));
    }

    public function testGetMatchedRouteParts()
    {
        $_SERVER['REQUEST_URI'] = 'posts/something';
        $parts = array(
            'module' => 'blog',
            'controller' => 'posts',
            'action' => 'show'
        );
        $this->assertEquals($parts, $this->_router->getMatchedRouteParts());

        $_SERVER['REQUEST_URI'] = 'foo/bar';
        $this->assertFalse($this->_router->getMatchedRouteParts());
    }
}
