<?php

/**
 * Tabouret
 *
 * @author    Kanat Gailimov <gailimov@gmail.com>
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 */

namespace tabouret;

/**
 * URL Router
 *
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Router
{
    /**
     * Routes
     *
     * @var array
     */
    private $_routes = array();

    /**
     * Adds route
     *
     * Usage example:
     *
     *     $router = new Router();
     *     $router->add('post', array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show'));
     *
     * @param  string $name Name
     * @param  array  $rule Rule
     * @return \tabouret\Router
     */
    public function add($name, array $rule)
    {
        if (!is_string($name))
            throw new \Exception('Route\'s name must be string');
        $this->_routes[$name] = $rule;

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
                list($module, $controller, $action) = explode('.', $rule[1]);
                $moduleDir = Registry::get('rootPath') . '/app/modules/' . $module;
                if (!is_dir($moduleDir))
                    throw new \Exception('Module "' . $module . '" not found');
                $controllerFile = $moduleDir . '/controllers/' . $controller . '.php';
                if (!file_exists($controllerFile))
                    throw new \Exception('Controller "' . $controller . '" in module "' . $module . '" not found');
                require_once $controllerFile;
                $func = $module . '\\controllers\\' . $controller . '\\' . $action;
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
     * Prepares and returns URN
     *
     * @return string
     */
    private function getUrn()
    {
        $urn = preg_replace('/^(.*?)index\.php$/is', '$1', $_SERVER['SCRIPT_NAME']);
        $urn = preg_replace('/^' . preg_quote($urn, '/') . '/is', '', urldecode($_SERVER['REQUEST_URI']));
        $urn = preg_replace('/(\/?)(\?.*)?$/is', '', $urn);

        return $urn;
    }
}
