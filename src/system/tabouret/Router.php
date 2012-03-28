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
 *     // Setting routes and getting parts of matched route
 *     $router->addRoute('home', array('^$', 'main.site.index'))
 *            ->addRoute('post', array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show'))
 *            ->getMatchedRouteParts();
 *
 *     // Alternatively, you can use the addRoutes() method to set more than one route at once
 *     $router->addRoutes(array(
 *         'home' => array('^$', 'main.site.index'),
 *         'post' => array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show')
 *     ));
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
     *     $router->addRoute('post', array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show'));
     *
     *     In this example we set route with name "post". Second argument is array of URL rule.
     *     It contains URL pattern and callback function separated by dot and consisting of module,
     *     controller and action.
     *
     * @param  string $name Name
     * @param  array  $rule Rule
     * @return \tabouret\Router
     */
    public function addRoute($name, array $rule)
    {
        $this->_routes[(string) $name] = $rule;

        return $this;
    }

    /**
     * Adds routes
     *
     * Usage example:
     *
     *     $router->addRoutes(array(
     *         'home' => array('^$', 'main.site.index'),
     *         'post' => array('^posts/(?P<slug>[-_a-z0-9а-я]+)$', 'blog.posts.show')
     *     ));
     *
     * @param  array $routes Routes
     * @return \tabouret\Router
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $name => $rule)
            $this->addRoute($name, $rule);

        return $this;
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
     * Returns true if the URL matches to the rule, otherwise returns false
     *
     * @param  string $url URL
     * @return bool
     */
    public function isMatched($url)
    {
        foreach ($this->_routes as $name => $rule) {
            if (preg_match('#' . $rule[0] . '#u', $url))
                return true;
        }

        return false;
    }

    /**
     * Returns matched route parts (module, controller, action)
     *
     * @return array|false If success returns matched route parts, otherwise returns false
     */
    public function getMatchedRouteParts()
    {
        foreach ($this->_routes as $name => $rule) {
            if (preg_match('#' . $rule[0] . '#u', $this->getUrn(), $matches)) {
                foreach ($matches as $key => $value) {
                    if (is_int($key))
                        continue;
                    $_GET[$key] = $value;
                }
                $rule = explode('.', $rule[1]);
                return array(
                    'module' => $rule[0],
                    'controller' => $rule[1],
                    'action' => $rule[2]
                );
            }
        }

        // Nothing matched
        return false;
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
}
