<?php
final class Yasui_Request
{
    private $_post;
    private $_get;
    private $_cookie;
    private $_data;

    public function __construct ()
    {
        if (ini_get('magic_quotes_gpc')) {
            $_GET = $this->_clean($_GET);
            $_POST = $this->_clean($_POST);
            $_COOKIE = $this->_clean($_COOKIE);
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

    public function baseURL()
    {
        return substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);
    }

    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST');
    }

    public function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    private function _clean ($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->_clean($value);
            }
        } else {
            $data = stripslashes($data);
        }

        return $data;
    }
}
