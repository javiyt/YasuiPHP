<?php
final class Framework_Request
{
    private $_post;
    private $_get;
    private $_cookie;
    private $_data;

    public function __construct ()
    {
        if (ini_get('magic_quotes_gpc')) {
            $_GET = $this->clean($_GET);
            $_POST = $this->clean($_POST);
            $_COOKIE = $this->clean($_COOKIE);
        }

        $this->_post = $_POST;
        $this->_get = $_GET;
        $this->_cookie = $_COOKIE;
    }

    public function __set ($name,$value)
    {
        $this->_data[$name] = $value;
    }

    public function __get ($name)
    {
        if (isset($this->_post[$name])) {
            return $this->_post[$name];
        }
        
        if (isset($this->_get[$name])) {
            return $this->_get[$name];
        }

        if (isset($this->_cookie[$name])) {
            return $this->_cookie[$name];
        }

        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }

        return false;
    }

    private function clean ($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->clean($value);
            }
        } else {
            $data = stripslashes($data);
        }

        return $data;
    }
}
