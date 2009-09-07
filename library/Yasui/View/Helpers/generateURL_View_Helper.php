<?php

class generateURL_View_Helper extends Yasui_View_Helper
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

        $request = Yasui_Registry::get('request');
        $html = '<a href="' . $request->baseURL() . $stringURL . '"';

        foreach($attr as $key => $value) {
            $html .= ' ' . $key .'="' . $value . '"';
        }

        $html .= '>' . $link . '</a>';

        return $html;
    }
}

