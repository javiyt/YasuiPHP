<?php
class IndexController extends Framework_Controller {

    public function indexAction()
    {
        $usuarios = new ModelUsuarios();
        $this->view->usuarios = $usuarios->getMunicipios();
    }
}

