<?php
require dirname(__FILE__).'Validate.php';

class Form_Validate {
    private $_error = null;

    private $_unicodeEnabled = false;

    private static $instance = null;

    private $lang = array();

    public function __construct($lang=array())
    {
        $this->_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        $this->lang = $lang;
    }

    public static function getInstance($lang)
    {
        if(self::$instance == null) {
            self::$instance = new self($lang);
        }
        return self::$instance;
    }

    public function validateRequired($value=null)
    {
        if ((string) $value === '') {
            $this->_error = $this->lang['required'];
            return false;
        } else {
            return true;
        }
    }

    public function validateAlnum($string=null,$whiteSpace=false,$allAlphabets=false)
    {
        if ((string)$string !== '') {
            $validate = new Validate();
            $format = VALIDATE_NUM.VALIDATE_ALPHA;
            if ($whiteSpace) {
                $format .= VALIDATE_SPACE;
            }
            if ($allAlphabets) {
                $format .= VALIDATE_EALPHA;
            }

            if (!$validate->string($string, array('format' => $format))) {
                $this->_error = $this->lang['nonalphanumeric'];
                return false;
            }
            return true;
        } else {
            $this->_error = $this->lang['emptyvalue'];
            return false;
        }
    }

    public function validateAlpha($string=null,$whiteSpace=false,$allAlphabets=false)
    {
        if ((string)$string !== '') {
            $validate = new Validate();
            $format = VALIDATE_ALPHA;
            if ($whiteSpace) {
                $format .= VALIDATE_SPACE;
            }
            if ($allAlphabets) {
                $format .= VALIDATE_EALPHA;
            }

            if (!$validate->string($string, array('format' => $format))) {
                $this->_error = $this->lang['nonalphabetics'];
                return false;
            }
            return true;
        } else {
            $this->_error = $this->lang['emptyvalue'];
            return false;
        }
    }

    public function validateDigits($value=null)
    {
        $filter = Form_Filter::getInstance();
        if ((string)$value !== '' && $value == $filter->filterDigits($value)) {
            return true;
        } else {
            $this->_error = $this->lang['nondigits'];
            return false;
        }
    }

    public function validateInt($value=null)
    {
        if ((int)$value === 0) {
            $this->_error = $this->lang['nonint'];
            return false;
        } else {
            return true;
        }
    }

    public function validateMoreThan($string=null,$limit=null)
    {
        if (is_int($string)) {
            if ((int) $string > (int) $limit) {
                return true;
            } else {
                $this->_error = $this->lang['nomore'].' '.(int) $limit;
                return false;
            }
        } else {
            if (strlen((string) $string) > (int) $limit) {
                return true;
            } else {
                $this->_error = $this->lang['stringnomore'].' '.(int) $limit.' '.$this->lang['characters'];
                return false;
            }
        }
    }

    public function validateLessThan($string=null,$limit=null)
    {
        if (is_int($string)) {
            if ((int) $string < (int) $limit) {
                return true;
            } else {
                $this->_error = $this->lang['noless'].' '.(int) $limit;
                return false;
            }
        } else {
            if (strlen((string) $string) < (int) $limit) {
                return true;
            } else {
                $this->_error = $this->lang['noless'].' '.(int) $limit.' '.$this->lang['characters'];
                return false;
            }
        }
    }

    public function validateBetween($string=null,$min=null,$max=null)
    {
        return ($this->validateMoreThan($string,$min) && $this->validateLessThan($string,$max));
    }

    public function validateRegExp($string=null,$regexp=null)
    {
        if (!preg_match($regexp,$string)) {
            $this->_error = $this->lang['valueinvalid'];
            return false;
        } else {
            return true;
        }
    }

    public function validateEmail($email=null)
    {
        //FROM http://www.linuxjournal.com/article/9585
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $this->_error = $this->lang['invalidemail'];
            return false;
        }
        $domain = substr($email, $atIndex+1);
        $local = substr($email, 0, $atIndex);

        $localLen = strlen($local);
        $domainLen = strlen($domain);

        if ($localLen < 1 || $localLen > 64) {
            $this->_error = $this->lang['invalidemail'];
            return false;
        }
        if ($local[0] == '.' || $local[$localLen-1] == '.' || preg_match('/\\.\\./', $local))  {
            $this->_error = $this->lang['invalidemail'];
            return false;
        }

        if ($domainLen < 1 || $domainLen > 255) {
            $this->_error = $this->lang['invalidemaildomain'];
            return false;
        }
        if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) || preg_match('/\\.\\./', $domain)) {
            $this->_error = $this->lang['invalidemaildomain'];
            return false;
        }

        if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))) {
            if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))) {
                $this->_error = $this->lang['invalidemail'];
                return false;
            }
        }
        if (function_exists('checkdnsrr') && false) {
            if (!checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")) {
                $this->_error = $this->lang['invalidemail'];
                return false;
            }
        }
        return true;
    }

    public function validateInarray($value=null,$array=null)
    {
        if ($value != null && $array != null) {
            if (!in_array($value,$array)) {
                $this->_error = $this->lang['valueinvalid'];
                return false;
            }
            return true;
        }
        $this->_error = $this->lang['emptyvalue'];
        return false;
    }

    public function validateURL($url=null)
    {
        if ($url != null) {
            $validate = new Validate();
            if ($validate->uri($url, array('domain_check' => true))) {
                return true;
            }
        }
        $this->_error = $this->lang['invalidurl'];
        return false;
    }

    public function getError()
    {
            return $this->_error;
    }
	
}
