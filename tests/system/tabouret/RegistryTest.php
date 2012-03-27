<?php

use tabouret\Registry;

class RegistryTest extends PHPUnit_Framework_TestCase
{
    public function testSetterAndGetter()
    {
        Registry::set('key', 'value');
        $this->assertEquals('value', Registry::get('key'));
    }
}
