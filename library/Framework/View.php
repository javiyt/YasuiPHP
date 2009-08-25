<?php
require dirname(__FILE__).'/View/Template.php';

final class Framework_View
{
    static private $intance = null;
    private $template;

    private function __construct ()
    {
        $this->template = new Template();
        $this->template->addTemplatePath(LAYOUT_ROOT);
    }

    public function getInstance()
    {
        if (self::$intance == null) {
            self::$intance = new Framework_View();
        }
        return self::$intance;
    }

    public function template ()
    {
        return $this->template;
    }

    public function __set ($name,$value)
    {
        $this->template->assign($name,$value);
    }
}

