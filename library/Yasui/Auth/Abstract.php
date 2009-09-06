<?php
abstract class Yasui_Auth_Abstract
{
    protected $_identity = null;
    protected $_credential = null;
    protected $_location = null;
    private $_salt = null;
    private $_cryptMethod = null;
    private $_cryptMethods = array('md5','sha1');
    protected $_session = null;

    abstract public function authenticate($identity, $credential);

    public function setAuthLocation($location)
    {
        $this->_location = $location;
        return $this;
    }

    public function setIdentityColumn($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

    public function setCredentialColumn($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    public function setCredentialCrypt($method, $salt=null)
    {
        $this->_salt = $salt;

        if (in_array($method,$this->_cryptMethods)) {
            $this->_cryptMethod = $method;
        }
        return $this;
    }

    public function isAuthenticate()
    {
        return isset($this->_session->identity);
    }


    protected function _getCrypCredential($credential)
    {
        if ($this->_cryptMethod != null) {
            if ($this->_salt != null) {
                $credential .= $this->_salt;
            }

            $function = $this->_cryptMethod;

            $credential = $function($credential);
        }
        return $credential;
    }

}

