<?php
require_once dirname(__FILE__).'/Savant3_plugin_baseURL.php';

class Savant3_Plugin_generateURL extends Savant3_Plugin
{
    public function generateURL(array $url,$link,$attr=array())
    {
        $stringURL = '';

        if (isset($url['controller'])) {
            $stringURL .= $url['controller'];
            unset($url['controller']);
        } else {
            $stringURL .= DEFAULT_CONTROLLER;
        }
        $stringURL .= '/';

        if (isset($url['action'])) {
            $stringURL .= $url['action'];
            unset($url['action']);
        } else {
            $stringURL .= DEFAULT_CONTROLLER;
        }
        $stringURL .= '/';

        foreach ($url as $key => $value) {
            $stringURL .= $key.'/'.$value.'/';
        }

        $base = new Savant3_Plugin_baseURL();
        $html = '<a href="' . $base->baseURL() . $stringURL . '"';

        foreach($attr as $key => $value) {
            $html .= ' ' . $key .'="' . $value . '"';
        }

        $html .= '>' . $link . '</a>';

        return $html;
    }
}

