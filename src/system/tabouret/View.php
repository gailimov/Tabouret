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
 * View renderer
 *
 * Usage example:
 *
 *     // Rendering view (with layout) from blog.posts.index with params
 *     // View template is detected automatically in app/modules/blog/views/posts/index.php
 *     // Variable is available using $posts
 *     View::render(array('posts' => $store->getPosts()));
 *
 *     // Rendering partial view (without layout) with params
 *     // You must specify path to view in /views/ directory (without .php)
 *     View::renderPartial('posts/_comments', array('post' => $post));
 *
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class View
{
    /**
     * Use module layout?
     *
     * @var bool
     */
    private static $_useModuleLayout = false;

    /**
     * Renders template
     *
     * @param  array  $params Params
     * @return string
     */
    public static function render($params = array())
    {
        echo self::fetch(App::getInstance()->getController() . '/' . App::getInstance()->getAction(), $params);
    }

    /**
     * Renders partial template
     *
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    public static function renderPartial($template, $params = array())
    {
        echo self::fetchPartialOfModule($template, $params);
    }

    /**
     * Sets module layout
     *
     * @return void
     */
    public static function useModuleLayout()
    {
        self::$_useModuleLayout = true;
    }

    /**
     * Fetches template
     *
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private static function fetch($template, $params = array())
    {
        $data = array('content' => self::fetchPartialOfModule($template, $params));

        return self::$_useModuleLayout
               ? self::fetchPartialOfModule('layouts/layout', $data)
               : self::fetchPartialOfApp('layouts/app', $data);
    }

    /**
     * Fetches partial template of application
     *
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private static function fetchPartialOfApp($template, $params = array())
    {
        return self::fetchPartial('views/' . $template . '.php', $params);
    }

    /**
     * Fetches partial template of module
     *
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private static function fetchPartialOfModule($template, $params = array())
    {
        return self::fetchPartial('modules/' . App::getInstance()->getModule() . '/views/' . $template . '.php',
                                  $params);
    }

    /**
     * Fetches partial template
     *
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private static function fetchPartial($template, $params = array())
    {
        extract($params);
        ob_start();
        $templateFile = App::getInstance()->config['appPath'] . '/' . $template;
        if (!file_exists($templateFile))
            throw new Exception('View file "' . $template . '" not found');
        include_once $templateFile;

        return ob_get_clean();
    }
}
