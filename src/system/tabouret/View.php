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
        echo self::fetchPartial($template, $params);
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
        $content = self::fetchPartial($template, $params);

        return self::fetchPartial('layouts/main', array('content' => $content));
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
        $templateFile = App::getInstance()->config['appPath'] . '/modules/' . App::getInstance()->getModule() .
                        '/views/' . $template . '.php';
        if (!file_exists($templateFile))
            throw new Exception('View file "' . $template . '.php" not found');
        include_once $templateFile;

        return ob_get_clean();
    }
}
