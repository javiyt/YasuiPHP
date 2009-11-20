<?php
final class Yasui_Registry
{
    static private $registry;

    static public function set($name,$value)
    {
        self::$registry[$name] = $value;
    }

    static public function get($name)
    {
        if (isset(self::$registry[$name])) {
            return self::$registry[$name];
        } else {
            return false;
        }
    }

    static public function exists($name)
    {
        return isset(self::$registry[$name]);
    }
}

