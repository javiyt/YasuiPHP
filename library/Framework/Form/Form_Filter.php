<?php

class Form_Filter {
    private $_unicodeEnabled = false;

    private static $instance = null;

    private function __construct()
    {
        $this->_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
    }

    public static function getInstance()
    {
        if(self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function filterAlnum ($string=null,$whiteSpace=false)
    {
        $whiteSpace = $whiteSpace ? '\s' : '';

        //The following code only works with utf8 encoded strings
        if (!$this->utf8_compliant($string)) {
            $string = utf8_encode($string);
        }

        if (!$this->_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9' . $whiteSpace . ']/';
        } else if (!$allAlphabets) {
            //The Alphabet means english alphabet.
            $pattern = '/[^a-zA-Z0-9'  . $whiteSpace . ']/u';
        } else {
            //The Alphabet means each language's alphabet.
            $pattern = '/[^\p{L}\p{N}' . $whiteSpace . ']/u';
        }
        return preg_replace($pattern, '', (string) $string);
    }

    public function filterAlpha ($string=null,$whiteSpace=false,$allAlphabets=false)
    {
        $whiteSpace = $whiteSpace ? '\s' : '';

        //The following code only works with utf8 encoded strings
        if (!$this->utf8_compliant($string)) {
            $string = utf8_encode($string);
        }

        if (!$this->_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z' . $whiteSpace . ']/';
        } else if (!$allAlphabets) {
            //The Alphabet means english alphabet.
            $pattern = '/[^a-zA-Z'  . $whiteSpace . ']/u';
        } else {
            //The Alphabet means each language's alphabet.
            $pattern = '/[^\p{L}' . $whiteSpace . ']/u';
        }
        return preg_replace($pattern, '', (string) $string);
    }

    public function filterDigits ($value=null)
    {
        if (!$this->_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative 0-9 match
            $pattern = '/[^0-9]/';
        } else if (extension_loaded('mbstring')) {
            // Filter for the value with mbstring
            $pattern = '/[^[:digit:]]/';
        } else {
            // Filter for the value without mbstring
            $pattern = '/[\p{^N}]/';
        }

        return preg_replace($pattern, '', (string) $value);
    }

    public function filterHtmlEntities ($string=null,$charset=null)
    {
        if ($charset === null && $this->utf8_compliant($string)) {
            $_charSet = 'UTF-8';
        } else {
            $_charSet = $charset;
        }
        return htmlentities($string,ENT_COMPAT,$_charSet);
    }

    public function filterInt ($value=null)
    {
        return (int) $value;
    }

    public function filterNewLines ($string=null)
    {
        return str_replace(array("\n", "\r"), '', $string);
    }

    public function filterStringLower ($string=null)
    {
        return strtolower($string);
    }

    public function filterStringUpper ($string=null)
    {
        return strtoupper($string);
    }

    public function filterTags ($string=null)
    {
        return strip_tags($string);
    }

    public function filterTrim ($string=null)
    {
        return trim((string) $string);
    }

    private function utf8_compliant($str)
    {
        if ( strlen($str) == 0 ) {
            return true;
        }
        // If even just the first character can be matched, when the /u
        // modifier is used, then it's valid UTF-8. If the UTF-8 is somehow
        // invalid, nothing at all will match, even if the string contains
        // some valid sequences
        return (preg_match('/^.{1}/us',$str) == 1);
    }
}