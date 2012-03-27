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
 * URL Router
 *
 * Usage example:
 *
 *     // Setting routes and dispatch URLs
 *     Router::getInstance()
 *         ->add('home', array('^$', 'main.site.index'))
 *         ->add('post', array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show'))
 *         ->dispatch();
 *
 *     // You can get current module, controller and action using following methods:
 *     Router::getInstance()->getModule(); // Returns current module
 *     Router::getInstance()->getController(); // Returns current controller
 *     Router::getInstance()->getAction(); // Returns current action
 *
 *     // You also can generate URLs from routes
 *     // Generates: /posts/something
 *     $router->createUrl('post', array('slug' => 'something'));
 *     // Generates: https://example.com/posts/something
 *     $router->createUrl('post', array('slug' => 'something'), true, true);
 *
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Router
{
    /**
     * Singleton instance
     *
     * @var \tabouret\Router
     */
    private static $_instance;

    /**
     * Routes
     *
     * @var array
     */
    private $_routes = array();

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
     * @return \tabouret\Router
     */
    public static function getInstance()
    {
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
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
     * Adds route
     *
     * Usage example:
     *
     *     $router->add('post', array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show'));
     *
     *     In this example we set route with name "post". Second argument is array of URL rule.
     *     It contains URL pattern and callback function separated by dot and consisting of module,
     *     controller and action.
     *
     * @param  string $name Name
     * @param  array  $rule Rule
     * @return \tabouret\Router
     */
    public function add($name, array $rule)
    {
        $this->_routes[(string) $name] = $rule;

        return $this;
    }

    /**
     * Dispatching
     *
     * @return void
     */
    public function dispatch()
    {
        foreach ($this->_routes as $name => $rule) {
            if (preg_match('#' . $rule[0] . '#u', $this->getUrn(), $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_int($key))
                        continue;
                    $_GET[$key] = $value;
                }
                list($this->_module, $this->_controller, $this->_action) = explode('.', $rule[1]);
                $moduleDir = Registry::get('rootPath') . '/app/modules/' . $this->_module;
                if (!is_dir($moduleDir))
                    throw new \Exception('Module "' . $this->_module . '" not found');
                $controllerFile = $moduleDir . '/controllers/' . $this->_controller . '.php';
                if (!file_exists($controllerFile))
                    throw new \Exception('Controller "' . $this->_controller . '" in module "' . $this->_module . '" not found');
                require_once $controllerFile;
                $func = $this->_module . '\\controllers\\' . $this->_controller . '\\' . $this->_action;
                if (!function_exists($func)) {
                    header('HTTP/1.1 404 Not Found');
                    die('Not Found');
                }
                call_user_func($func);
                return;
            }
        }

        // Nothing matched - 404
        header('HTTP/1.1 404 Not Found');
        die('Not Found');
    }

    /**
     * Creates URL
     *
     * Usage example:
     *
     *     // Our route: $router->add('post', array('^posts/(?P<slug>[-_a-z0-9]+)$', 'blog.posts.show'));
     *
     *     // Generates: /posts/something
     *     $router->createUrl('post', array('slug' => 'something'));
     *     // Generates: https://example.com/posts/something
     *     $router->createUrl('post', array('slug' => 'something'), true, true);
     *
     * @param  string $name     Route name
     * @param  array  $params   Params
     * @param  bool   $absolute URL should be absolute?
     * @param  bool   $https    Use HTTPS?
     * @return string
     */
    public function createUrl($name, array $params = null, $absolute = false, $https = false)
    {
        foreach ($this->_routes as $routeName => $rule) {
            if ($routeName == (string) $name) {
                $url = $rule[0];
                while (preg_match('#\([^()]++\)#', $url, $matches)) {
                    // Search for the matched value
                    $search = $matches[0];
                    // Remove the parenthesis from the match as the replace
                    $replace = substr($matches[0], 1, -1);
                    while (preg_match('#^\?P?<(\w+)>(.*?)$#', $replace, $matches)) {
                        list($key, $param) = $matches;
                        if (isset($params[$param])) {
                            // Replace the key with the parameter value
                            $replace = str_replace($key, $params[$param], $replace);
                        } else {
                            // This group has missing parameters
                            $replace = '';
                            break;
                        }
                    }

                    // Replace the group in the URL
                    $url = str_replace($search, $replace, $url);
                }

                $url = str_replace('^', '', $url);
                $url = str_replace('$', '', $url);
                $url = preg_replace('#//+#', '/', rtrim($url, '/'));

                if (!$absolute)
                    return $this->getRelativeUrl() . $url;
                return $this->getHostInfo($https) . $this->createUrl($name, $params);
            }
        }
    }

    /**
     * Prepares and returns URN
     *
     * @return string
     */
    private function getUrn()
    {
        $urn = $this->getRelativeUrl();
        $urn = preg_replace('/^' . preg_quote($urn, '/') . '/is', '', urldecode($_SERVER['REQUEST_URI']));
        $urn = preg_replace('/(\/?)(\?.*)?$/is', '', $urn);

        return $urn;
    }

    /**
     * Returns relative URL
     *
     * @return string
     */
    private function getRelativeUrl()
    {
        return preg_replace('/^(.*?)index\.php$/is', '$1', $_SERVER['SCRIPT_NAME']);
    }

    /**
     * Returns the scheme and host part of the URI
     *
     * @param  bool $https Use HTTPS?
     * @return string
     */
    private function getHostInfo($https = false)
    {
        return ($https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
