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
 * Exception for 404 errors
 *
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class NotFoundException extends Exception
{
    /**
     * Handles exception
     *
     * @param  string $message Error message
     * @return mixed
     */
    public function handle($message)
    {
        header('HTTP/1.1 404 Not Found');

        list($module, $controller, $action) = explode('.', App::getInstance()->config['error404Action']);

        $controllerFile = App::getInstance()->config['appPath'] . '/modules/' .
                          $module . '/controllers/' . $controller . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $func = '\\' . $module . '\\' . $controller . '\\' . $action;
            if (function_exists($func))
                return $func($message);
        }

        echo $message;

        return;
    }
}
