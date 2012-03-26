<?php

/**
 * Tabouret
 *
 * @author    Kanat Gailimov <gailimov@gmail.com>
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 */

namespace tabouret;

/**
 * Registry
 *
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Registry
{
    /**
     * Singleton instance
     *
     * @var \tabouret\Registry
     */
    private static $_instance;

    /**
     * Registry
     *
     * @var array
     */
    private $_registry = array();

    /**
     * Sets value by key
     *
     * @param  string $key   Key
     * @param  mixed  $value Value
     * @return void
     */
    public static function set($key, $value)
    {
        self::getInstance()->_registry[(string) $key] = $value;
    }

    /**
     * Returns value by key
     *
     * @param  string $key Key
     * @return mixed
     */
    public static function get($key)
    {
        return self::getInstance()->_registry[(string) $key];
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Returns singleton instance
     *
     * @return \tabouret\Registry
     */
    private static function getInstance()
    {
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }
}
