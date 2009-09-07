<?php
abstract class Yasui_View_Helper
{
    public function __construct($configuration=array())
    {
        $configuration = (array)$configuration;
        foreach($configuration as $key => $val) {
            $this->$key = $val;
        }
    }
}
