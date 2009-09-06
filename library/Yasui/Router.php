<?php
final class Yasui_Router
{
    
    static public function getRoute ()
    {
        $request = Yasui_Registry::get('request');
        $return = array('controller' => DEFAULT_CONTROLLER,'action' => DEFAULT_ACTION);

        if ($request->route) {
            $router = explode('/',$request->route);

            $tmp = array_shift($router);
            if (isset($tmp)) {
                $return['controller'] = preg_replace('/\W/','',$tmp);
            }

            $tmp = array_shift($router);
            if (isset($tmp)) {
                $return['action'] = preg_replace('/\W/','',$tmp);
            }

            $limite = count($router);
            for($i=0;$i<$limite;$i=$i+2) {
                $request->$$router[$limite] = $router[$limite + 1];
            }
        }

        return $return;
    }
}
