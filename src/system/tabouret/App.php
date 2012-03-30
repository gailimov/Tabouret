<?php

/**
 * Tabouret — Lightweight PHP Framework
 * "Simple as a tabouret"
 *
 * For the license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @author    Kanat Gailimov <gailimov@gmail.com>
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 */

namespace tabouret;

/**
 * Front Controller
 *
 * Usage example:
 *
 *     // Running application
 *     App::getInstance()->init($config)->run();
 *
 *     // You can access to the config using property
 *     App::getInstance()->config['site']['title'];
 *
 *     // For getting the object of router, use the getRouter() method
 *     App::getInstance()->getRouter();
 *
 *     // You can get current module, controller and action using following methods:
 *     App::getInstance()->getModule(); // Returns current module
 *     App::getInstance()->getController(); // Returns current controller
 *     App::getInstance()->getAction(); // Returns current action
 *
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class App
{
    /**
     * Config
     *
     * @var array
     */
    public $config = array();

    /**
     * Singleton instance
     *
     * @var \tabouret\App
     */
    private static $_instance;

    /**
     * @var \tabouret\Router
     */
    private $_router;

    /**
     * Module
     *
     * @var string
     */
    private $_module;

    /**
     * Controller
     *
     * @var string
     */
    private $_controller;

    /**
     * Action
     *
     * @var string
     */
    private $_action;

    /**
     * Returns singleton instance
     *
     * @return \tabouret\App
     */
    public static function getInstance()
    {
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Initialization
     *
     * @param  string $config Path to config file
     * @return \tabouret\App
     */
    public function init($config)
    {
        return $this->initConfig($config)->initLoader();
    }

    /**
     * Running
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->dispatch();
        } catch (NotFoundException $e) {
            $e->handle($e->getMessage());
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Returns router object
     *
     * @return \tabouret\Router
     */
    public function getRouter()
    {
        if (!$this->_router)
            $this->_router = new Router();
        return $this->_router;
    }

    /**
     * Returns module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Returns controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Returns action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Creates URL
     *
     * @see \tabouret\Router::createUrl()
     *
     * @param  string $name     Route name
     * @param  array  $params   Params
     * @param  bool   $absolute URL should be absolute?
     * @param  bool   $https    Use HTTPS?
     * @return string
     */
    public function createUrl($name, array $params = null, $absolute = false, $https = false)
    {
        return $this->getRouter()->createUrl($name, $params, $absolute, $https);
    }

    /**
     * Initializes config
     *
     * @param  string $config Path to config file
     * @return \tabouret\App
     */
    private function initConfig($config)
    {
        if (!file_exists($config)) {
            require_once 'Exception.php';
            throw new Exception('Config file "' . $config . '" not found');
        }
        $this->config = require_once $config;

        if (!isset($this->config['appPath']))
            $this->config['appPath'] = __DIR__ . '/../../app';

        return $this;
    }

    /**
     * Initializes loader
     *
     * @return \tabouret\App
     */
    private function initLoader()
    {
        require_once __DIR__ . '/../vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';

        $loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
        $loader->registerNamespaces(array(
            'tabouret' => __DIR__ . '/..'
        ));
        $loader->register();

        return $this;
    }

    /**
     * Dispatching
     *
     * @return mixed
     */
    private function dispatch()
    {
        $options = $this->getRouter()->addRoutes($this->config['routes'])->getMatchedRouteParts();

        if (!$options)
            throw new NotFoundException('404 Not Found');

        $this->_module = $options['module'];
        $this->_controller = $options['controller'];
        $this->_action = $options['action'];

        $moduleDir = $this->config['appPath'] . '/modules/' . $this->_module;

        if (!is_dir($moduleDir))
            throw new Exception('Module "' . $this->_module . '" not found');

        $controllerFile = $moduleDir . '/controllers/' . $this->_controller . '.php';

        if (!file_exists($controllerFile))
            throw new Exception('Controller "' . $this->_controller . '" in module "' . $this->_module . '" not found');

        require_once $controllerFile;

        $func = $this->_module . '\\' . $this->_controller . '\\' . $this->_action;

        if (!function_exists($func))
            throw new NotFoundException('404 Not Found');

        call_user_func($func);
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
