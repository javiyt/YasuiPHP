<?php

class Yasui_View_Helper_generateURL extends Yasui_View_Helper
{
    public function generateURL(array $url, $link, $attr=array())
    {
        $router = Yasui_Registry::get('router');
        $html = '<a href="' . $router->getURL($url) . '"';

        foreach($attr as $key => $value) {
            $html .= ' ' . $key .'="' . $value . '"';
        }

        $html .= '>' . $link . '</a>';

        return $html;
    }
}

