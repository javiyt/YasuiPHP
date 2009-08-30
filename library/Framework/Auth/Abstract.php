<?php
abstract class Framework_Auth_Abstract
{
    protected $_identity;
    protected $_credential;

    public function setIdentity($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

}

