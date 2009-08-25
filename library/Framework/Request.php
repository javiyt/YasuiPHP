<?php
final class Framework_Request
{
    private $post;
    private $get;
    private $cookie;
    private $data;

    public function __construct ()
    {
        if (ini_get('magic_quotes_gpc')) {
            $_GET = $this->clean($_GET);
            $_POST = $this->clean($_POST);
            $_COOKIE = $this->clean($_COOKIE);
        }

        $this->post = $_POST;
        $this->get = $_GET;
        $this->cookie = $_COOKIE;
    }

    public function __set ($name,$value)
    {
        $this->data[$name] = $value;
    }

    public function __get ($name)
    {
        if (isset($this->post[$name])) {
            return $this->post[$name];
        }
        
        if (isset($this->get[$name])) {
            return $this->get[$name];
        }

        if (isset($this->cookie[$name])) {
            return $this->cookie[$name];
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
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
