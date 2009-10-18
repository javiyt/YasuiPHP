<?php
final class Yasui_Router
{
    private $_data = array('module' => null, 'controller' => DEFAULT_CONTROLLER, 'action' => DEFAULT_ACTION);
    private $_routes = array();
    private $_request;

    public function __construct()
    {
        $this->_request = Yasui_Registry::get('request');

        if ($this->_request->route) {
            $routIni = new Yasui_Config('routes.ini', 'ini');
            $this->_routes = $routIni->toArray();

            $route = $this->_routeExists($this->_request->route);
            if ($route) {
                $variables = $this->_map($this->_request->route, $route);
                foreach($variables as $key => $value) {
                    $this->_request->$key = $value;
                }
            } else {
                $router = explode('/', trim($this->_request->route, '/'));

                if (count($router) > 0) {
                    //First parameter in $_GET['router'] can be module or controller
                    $tmp = preg_replace('/\W/', '', array_shift($router));
                    if (isset($tmp)) {
                        if (is_dir(APPLICATION_ROOT . CONTROLLER_ROOT . $tmp)) {
                            $this->_data['module'] = $tmp;
                        } else {
                            $this->_data['controller'] = $tmp;
                        }
                    }
                }

                if (count($router) > 0) {
                    //Second parameter in $_GET['router'] can be controller or action
                    $tmp = preg_replace('/\W/', '', array_shift($router));
                    if (isset($tmp)) {
                        //Module not null, so the controller hasnt been set, so the second parameter is the controller
                        //and the thrid parameter is the action
                        if ($this->_data['module'] != null) {
                            $this->_data['controller'] = $tmp;
                        } else {
                            $this->_data['action'] = $tmp;
                        }
                    }
                }

                if (count($router) > 0) {
                    //Have to extract third parameter because it is the action
                    if ($this->_data['module'] != null) {
                        $tmp = preg_replace('/\W/', '', array_shift($router));
                        if (isset($tmp)) {
                            $this->_data['action'] = $tmp;
                        }
                    }
                }

                $limite = count($router);
                for($i=0;$i<$limite;$i=$i+2) {
                    $this->_request->$router[$i] = $router[$i + 1];
                }
            }
        }


    }

    public function __get($name)
    {
        return (isset($this->_data[$name])) ? $this->_data[$name] : false;
    }

    public function getURL(array $route)
    {
        //If is not set the url's controller
        if (!isset($route['controller'])) {
            $route['controller'] = $this->_data['controller'];
        }
        //If is not set the url's action
        if (!isset($route['action'])) {
            $route['action'] = $this->_data['action'];
        }

        $url = '';
        foreach($this->_routes as $keyRoute => $valueRoute) {

            $keyRouteMap = explode('/', trim($keyRoute, '/'));
            
            if ((isset($valueRoute['module']) && $route['module'] != $valueRoute['module']) || (isset($valueRoute['module']) && !in_array($route['module'], $keyRouteMap))) {
                continue;
            }

            if ((isset($valueRoute['controller']) && $route['controller'] != $valueRoute['controller']) || (!isset($valueRoute['controller']) && !in_array($route['controller'], $keyRouteMap))) {
                continue;
            }

            if ((isset($valueRoute['action']) && $route['action'] != $valueRoute['action']) || (!isset($valueRoute['action']) && !in_array($route['action'], $keyRouteMap))) {
                continue;
            }

            $url = $this->_request->baseURL() . $keyRoute;

            if (preg_match('/:[a-zA-Z]*/', $keyRoute, $encontrados)) {
                if (count($encontrados) != (count($route) - 2)) {
                    break;
                }

                foreach($encontrados as $value) {
                    $url = str_replace($value, $route[substr($value, 1)], $url);
                }
            }
            break;
        }

        if ($url == '') {
            $url = $this->_request->baseURL() . $route['controller'] . '/' . $route['action'];
            unset($route['controller']);
            unset($route['action']);

            foreach($route as $key => $value) {
                $url .= '/' . $key . '/' . $value;
            }
        }
        
        return $url;
    }

    private function _routeExists($route)
    {
        $routemap = explode('/', trim($route, '/'));
        foreach($this->_routes as $key => $values) {
            $keymap = explode('/', trim($key, '/'));
            if (count($keymap) != count($routemap)) {
                continue;
            }
            if ($keymap[0] != $routemap[0]) {
                continue;
            }
            return $key;
        }

        return false;
    }

    private function _map($url, $route)
    {
        $map = $this->_routes[$route];
        $routemap = explode('/', trim($route, '/'));
        $urlmap = explode('/', trim($url, '/'));
        $vars = array();

        if (isset($map['module'])) {
            $this->_data['module'] = $map['module'];
        }

        if (isset($map['controller'])) {
            $this->_data['controller'] = $map['controller'];
        } else {
            $this->_data['controller'] = array_shift($urlmap);
        }

        if (isset($map['action'])) {
            $this->_data['action'] = $map['action'];
        } else {
            $this->_data['action'] = array_shift($urlmap);
        }

        foreach($routemap as $var) {
            $tmp = array_shift($urlmap);
            if (substr($var,0,1) == ':') {
                $vars[substr($var,1)] = $tmp;
            }
        }

        return $vars;
    }
}