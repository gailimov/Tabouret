<?php

use tabouret\App;

class AppTest extends PHPUnit_Framework_TestCase
{
    private $_app;

    public function setUp()
    {
        $this->_app = App::getInstance();
        $_SERVER['REQUEST_URI'] = 'posts/something';
        $this->_app->run();
    }

    public function testGetInstance()
    {
        $this->assertTrue(App::getInstance() === App::getInstance());
    }

    public function testInit()
    {
        try {
            $this->_app->init('foo');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Expected exception for bad config path');
    }

    public function testAccessToConfig()
    {
        $this->assertEquals(array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show'),
                            $this->_app->config['routes']['post']);
    }

    public function testRun()
    {
        $_SERVER['REQUEST_URI'] = 'foo/bar';
        try {
            $this->_app->run();
        } catch (Exception $e) {
            return;
        }
        $this->fail('Expected exception for 404 error');
    }

    public function testGetRouter()
    {
        $this->assertTrue($this->_app->getRouter() === $this->_app->getRouter());
    }

    public function testGetModule()
    {
        $this->assertEquals('blog', $this->_app->getModule());
    }

    public function testGetController()
    {
        $this->assertEquals('posts', $this->_app->getController());
    }

    public function testGetAction()
    {
        $this->assertEquals('show', $this->_app->getAction());
    }
}
