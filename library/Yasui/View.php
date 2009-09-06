<?php
require 'Yasui/View/Template.php';

final class Yasui_View
{
    static private $intance = null;
    private $_template;

    private function __construct ()
    {
        $this->_template = new Template();
        $this->_template->addTemplatePath(LAYOUT_ROOT);
    }

    public function getInstance()
    {
        if (self::$intance == null) {
            self::$intance = new self();
        }
        return self::$intance;
    }

    public function template ()
    {
        return $this->_template;
    }

    public function __set ($name,$value)
    {
        $this->_template->assign($name,$value);
    }
}
