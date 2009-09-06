<?php

/**
* Form generation and validation
* @todo JQuery validation plugin
* @todo Select elements cant contain equals key values in options
* @todo Checkbox element can check differents values
* @todo Create file element
* @todo Create multiselect element
*
*/

require_once 'Yasui/Validate/Validate.php';
require_once 'Yasui/Filter/Filter.php';

abstract class Yasui_Form
{

    private $_formName = null;
    private $_formId = null;
    private $_formMethod = 'post';
    private $_formType = 'application/x-www-form-urlencoded';
    private $_formAction = null;
    private $_formAttribs = array();
    private $_formElements = array();
    private $_formTypes = array('text','textarea','button','submit','image','select','radio','checkbox','password','hidden');
    private $_formDecorators = array('HtmlTag' => array('tag' => 'dd'),'Label' => array('tag' => 'dt'));
    private $_stopNotValid = false;
    private $_error = null;
    private $_lang = array();
    private $_filter = null;
    private $_validate = null;

    public function __construct ($name=null,$lang=array())
    {
        if ($name != null) {
            $this->_formName = $name;
        }
        $this->_lang = $lang;
        return $this;
    }

    public function __toString()
    {
        return $this->parseForm(true,true);
    }

    public function setName ($name=null)
    {
        if ($name != null) {
            $this->_formName = $name;
        }
        return $this;
    }

    public function setId ($id=null)
    {
        if ($id != null) {
            $this->_formId = $id;
        }
        return $this;
    }

    public function setMethod ($method=null)
    {
        if ($method != null && in_array($method,array('post','get'))) {
            $this->_formMethod = $method;
        }
        return $this;
    }

    public function setType ($type=null)
    {
        if ($type != null && in_array($type,array('application/x-www-form-urlencoded','multipart/form-data','text/plain'))) {
            $this->_formType = $type;
        }
        return $this;
    }

    public function setAction ($action=null)
    {
        if ($action != null) {
            $this->_formAction = $action;
        }
        return $this;
    }

    public function setStopNotValid($stop=false)
    {
        $this->_stopNotValid = (boolean) $stop;
    }

    public function setDecorators($HtmlTag=array(),$Label=array())
    {
        if (is_array($HtmlTag) && is_array($Label)) {
            if (isset($HtmlTag['tag'])) {
                $this->_formDecorators['HtmlTag'] = $HtmlTag;
            }
            if (isset($Label['tag'])) {
                $this->_formDecorators['Label'] = $Label;
            }
        }
        return $this;
    }

    public function setValue($name=null,$value=null)
    {
        if ($name != null && $value != null) {
            if ($this->_formElements[$name]['type'] != 'select' && $this->_formElements[$name]['type'] != 'radio' && $this->_formElements[$name]['type'] != 'checkbox') {
                $this->_formElements[$name]['attribs']['value'] = $value;
            } else {
                $this->_formElements[$name]['attribs']['selected'] = $value;
            }
        }
    }

    public function setValues($values=array())
    {
        foreach ($values as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    public function setLabel($name=null,$label=null)
    {
        if ($name != null && $label != null) {
            $this->_formElements[$name]['label'] = $label;
        }
    }

    /*
    *	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS
    *	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS
    *	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS ADD FUNCTIONS	ADD FUNCTIONS
    */

    public function addAttrib ($name=null,$value=null)
    {
        if ($name != null) {
            if ($value == null) {
                unset($this->_formAttribs[$name]);
            }
            else {
                $this->_formAttribs[$name] = $value;
            }
        }
        return $this;
    }

    public function addAttribs ($attribs=array())
    {
        if (is_array($attribs)) {
            foreach ($attribs as $key => $value) {
                $this->addAttrib($key,$value);
            }
        }
        return $this;
    }

    public function addElement ($type=null,$name=null,$label=null,$attribs=array())
    {
        if ($type != null && $name != null && in_array($type,$this->_formTypes)) {
            $this->_formElements[$name]['type'] = $type;
            $this->_formElements[$name]['label'] = (string)$label;
            $this->_formElements[$name]['attribs'] = $attribs;
        }
        return $this;
    }

    public function addFilter($name=null,$type=null,$options=array())
    {
        if ($name != null) {
            if (array_key_exists($name,$this->_formElements)) {
                $this->_formElements[$name]['filters'][$type] = $options;
            }
        }
        return $this;
    }

    public function addFilters($name=null,$type=null)
    {
        if ($name != null) {
            if (array_key_exists($name,$this->_formElements) && is_array($type)) {
                foreach ($type as $filter) {
                    if (is_array($filter)) {
                        $filterType = $filter[0];
                        unset($filter[0]);
                        $this->_formElements[$name]['filters'][$filterType] = $filter;
                    } else {
                        $this->_formElements[$name]['filters'][$filter] = array();
                    }
                }
            }
        }
        return $this;
    }

    public function addValidate($name=null,$type=null,$options=array())
    {
        if ($name != null) {
            if (array_key_exists($name,$this->_formElements)) {
                $this->_formElements[$name]['validate'][$type] = $options;
            }
        }
        return $this;
    }

    public function addValidates($name=null,$type=null)
    {
        if ($name != null) {
            if (array_key_exists($name,$this->_formElements) && is_array($type)) {
                foreach ($type as $validate) {
                    if (is_array($validate)) {
                        $validateType = $validate[0];
                        unset($validate[0]);
                        $this->_formElements[$name]['validate'][$validateType] = $validate;
                    } else {
                        $this->_formElements[$name]['validate'][$validate] = array();
                    }
                }
            }
        }
        return $this;
    }
    /*
    * PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS
    * PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS
    * PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS PARSE FUNCTIONS
    */

    private function parseForm ($withErrors=false,$withValues=false)
    {
        $xHtml = '<form';

        if ($this->_formName != null) {
            $xHtml .= " name=\"{$this->_formName}\"";
        }

        if ($this->_formId != null) {
            $xHtml .= " id=\"{$this->_formId}\"";
        } elseif ($this->_formId == null && $this->_formName != null) {
            $xHtml .= " id=\"{$this->_formName}\"";
        }

        if ($this->_formAction != null) {
            $xHtml .= " action=\"{$this->_formAction}\"";
        } else {
            $xHtml .= ' action=""';
        }

        if ($this->_formType != null) {
            $xHtml .= " enctype=\"{$this->_formType}\"";
        }

        if ($this->_formMethod != null && in_array($this->_formMethod,array('post','get'))) {
            $xHtml .= " method=\"{$this->_formMethod}\"";
        } else {
            $xHtml .= ' method="post"';
        }

        foreach ($this->_formAttribs as $attrib => $attribValue) {
            $xHtml .= " $attrib=\"$attribValue\"";
        }

        $xHtml .= '>';

        if ($this->_formDecorators['HtmlTag']['tag'] == 'dd') {
            $xHtml .= '<dl>';
        } else {
            $xHtml .= '<fieldset>';
        }

        foreach ($this->_formElements as $name => $values) {
            if ($values['label'] != null && $values['type'] != 'button' && $values['type'] != 'submit' && $values['type'] != 'image') {
                $xHtml .= $this->parseLabel($name);
            }

            switch ($values['type']) {
                case 'text':
                    if ($withValues) {
                        $this->_formElements[$name]['attribs']['value'] = $this->getValue($name,false);
                    }
                    $xHtml .= $this->parseText($name);
                    break;
                case 'textarea':
                    if ($withValues) {
                        $this->_formElements[$name]['attribs']['value'] = $this->getValue($name,false);
                    }
                    $xHtml .= $this->parseTextarea($name);
                    break;
                case 'password':
                    $xHtml .= $this->parsePassword($name);
                    break;
                case 'select':
                    if ($withValues) {
                        $this->_formElements[$name]['attribs']['selected'] = $this->getValue($name,false);
                    }
                    $xHtml .= $this->parseSelect($name);
                    break;
                case 'radio':
                case 'checkbox':
                    if ($withValues) {
                        $this->_formElements[$name]['attribs']['selected'] = $this->getValue($name,false);
                    }
                    $xHtml .= $this->parseRadio($name);
                    break;
                case'button':
                case 'submit':
                case 'image':
                    $xHtml .= $this->parseButton($name);
                    break;
                case 'hidden':
                    if ($withValues) {
                        $this->_formElements[$name]['attribs']['value'] = $this->getValue($name,false);
                    }
                    $xHtml .= $this->parseHidden($name);
                    break;
            }

            if ($withErrors) {
                $xHtml .= $this->parseElementErrors($name);
            }
        }

        if ($this->_formDecorators['HtmlTag']['tag'] == 'dd') {
            $xHtml .= '</dl>';
        } else {
            $xHtml .= '</fieldset>';
        }

        $xHtml .= '</form>';
        return $xHtml;
    }

    private function parseLabel ($name=null)
    {
        $xHtml = '';
        if ($name != null) {
            if ($this->_formDecorators['Label']['tag'] != '') {
                $xHtml .= '<'.$this->_formDecorators['Label']['tag'].'>';
            }

            $xHtml .= "<label for=\"$name\">{$this->_formElements[$name]['label']}</label>";
            
            if ($this->_formDecorators['Label']['tag'] != '') {
                $xHtml .= '</'.$this->_formDecorators['Label']['tag'].'>';
            }
        }
        return $xHtml;
    }

    private function parseElementDecorator ($xHtml=null)
    {
        if ($xHtml != null && $this->_formDecorators['HtmlTag']['tag'] != '') {
            $xHtml = '<'.$this->_formDecorators['HtmlTag']['tag'].'>'.
                    $xHtml .
                    '</'.$this->_formDecorators['HtmlTag']['tag'].'>';
        }
        return $xHtml;
    }

    private function parseElementErrors($name)
    {
        $xHtml = '';
        if (!$this->validateField($name)) {
            $errors = split("\n",$this->_error);
            $xHtml = '<ul>';
            foreach ($errors as $error) {
                if ((string) $error !== '') {
                    $xHtml .= "<li>$error</li>";
                }
            }
            $xHtml .= '</ul>';
        }
        return $xHtml;
    }

    private function parseText ($name=null)
    {
        $xHtml = '';
        if ($name != null) {
            $xHtml = "<input type=\"text\" name=\"$name\" id=\"$name\"";
            foreach ($this->_formElements[$name]['attribs'] as $attrib => $attribValue) {
                $xHtml .= " $attrib=\"$attribValue\"";
            }
            $xHtml .= " />";
        }
        return $this->parseElementDecorator($xHtml);
    }

    private function parsePassword ($name=null)
    {
        $xHtml = '';
        if ($name != null) {
            $xHtml = "<input type=\"password\" name=\"$name\" id=\"$name\"";
            foreach ($this->_formElements[$name]['attribs'] as $attrib => $attribValue) {
                $xHtml .= " $attrib=\"$attribValue\"";
            }
            $xHtml .= " />";
        }
        return $this->parseElementDecorator($xHtml);
    }


    private function parseTextarea ($name=null)
    {
        $xHtml = '';
        if ($name != null) {
            $xHtml = "<textarea name=\"$name\" id=\"$name\"";
            foreach ($this->_formElements[$name]['attribs'] as $attrib => $attribValue) {
                if ($attrib != 'value') {
                    $xHtml .= " $attrib=\"$attribValue\"";
                }
            }
            $xHtml .= ">{$this->_formElements[$name]['attribs']['value']}</textarea>";
        }
        return $this->parseElementDecorator($xHtml);
    }

    private function parseButton ($name=null)
    {
        $xHtml = '';
        if ($name != null) {
            $xHtml .= "<input type=\"{$this->_formElements[$name]['type']}\" name=\"$name\" id=\"$name\"";
            if (isset($this->_formElements[$name]['label']) && $this->_formElements[$name]['label'] != null) {
                if ($this->_formElements[$name]['type'] == 'image') {
                    $xHtml .= ' src="'.$this->_formElements[$name]['label'].'"';
                    unset($this->_formElements[$name]['attribs']['src']);
                }
                else {
                    $xHtml .= ' value="'.$this->_formElements[$name]['label'].'"';
                    unset($this->_formElements[$name]['attribs']['value']);
                }
            }
            foreach ($this->_formElements[$name]['attribs'] as $attrib => $attribValue) {
                $xHtml .= " $attrib=\"$attribValue\"";
            }
            $xHtml .= " />";
        }
        return $this->parseElementDecorator($xHtml);
    }

    private function parseSelect ($name=null)
    {
        $xHtml = '';
        $optionsArray = array();
        if ($name != null) {
            $xHtml = "<select name=\"$name\" id=\"$name\"";
            foreach ($this->_formElements[$name]['attribs'] as $attrib => $attribValue) {
                if ($attrib != 'value' && $attrib != 'selected') {
                    $xHtml .= " $attrib=\"$attribValue\"";
                }
            }
            $xHtml .= '>';

            foreach ($this->_formElements[$name]['attribs']['value'] as $key => $val) {
            if (is_array($val)) {
                $xHtml .= "<optgroup label=\"$key\">";
                foreach ($val as $key2 => $val2) {
                $xHtml .= "<option value=\"$key2\"";
                if ($this->_formElements[$name]['attribs']['selected'] == $key2) {
                $xHtml .= ' selected="selected"';
                }
                $xHtml .= ">$val2</option>";
                $optionsArray[] = $key2;
                }
                $xHtml .= '</optgroup>';
            } else {
            $xHtml .= "<option value=\"$key\"";
            if ($this->_formElements[$name]['attribs']['selected'] == $key) {
            $xHtml .= ' selected="selected"';
            }
            $xHtml .= ">$val</option>";
            $optionsArray[] = $key;
            }
            }
            $xHtml .= '</select>';
            $this->addValidate($name,'InArray',array('array' => $optionsArray));
        }

        return $this->parseElementDecorator($xHtml);
    }

    private function parseRadio ($name=null)
    {
        $xHtml = '';
        if ($name != null) {
            $comun = "<input type=\"{$this->_formElements[$name]['type']}\" name=\"$name\" id=\"$name\"";
            foreach ($this->_formElements[$name]['attribs'] as $attrib => $attribValue) {
                if ($attrib != 'value' && $attrib != 'selected') {
                    $comun .= " $attrib=\"$attribValue\"";
                }
            }
            foreach ($this->_formElements[$name]['attribs']['value'] as $key => $value) {
                $xHtml .= $comun." value=\"$key\"";
                if ($key == $this->_formElements[$name]['attribs']['selected']) {
                    $xHtml .= ' checked="checked"';
                }
                $xHtml .= " /> $value";
            }
        }
        return $this->parseElementDecorator($xHtml);
    }

    private function parseHidden($name=null)
    {
        $xHtml = '';
        if ($name != null) {
            $xHtml = "<input type=\"hidden\" name=\"$name\" id=\"$name\"";
            foreach ($this->_formElements[$name]['attribs'] as $attrib => $attribValue) {
                $xHtml .= " $attrib=\"$attribValue\"";
            }
            $xHtml .= ' />';
        }
        return $xHtml;
    }

    /*
    * FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS
    * FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS
    * FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS FILTER FUNCTIONS
    */
    public function getValue($name=null,$filtered=true)
    {
        if ($name != null) {
            if (array_key_exists($name,$this->_formElements)) {
                if ($this->_formMethod == 'post') {
                    $value = $_POST[$name];
                } else {
                    $value = $_GET[$name];
                }

                if ($filtered && is_array($this->_formElements[$name]['filters'])) {

                    if ($this->_filter == null) {
                        $this->_filter = new Yasui_Filter();
                    }

                    foreach ($this->_formElements[$name]['filters'] as $filter => $options) {
                        switch ($filter) {
                            case 'Alnum':
                                $value = $this->_filter->filterAlnum($value,$options['whiteSpace'],$options['allAlphabets']);
                                break;
                            case 'Alpha':
                                $value = $this->_filter->filterAlpha($value,$options['whiteSpace'],$options['allAlphabets']);
                                break;
                            case 'Digits':
                                $value = $this->_filter->filterDigits($value);
                                break;
                            case 'HtmlEntities':
                                $value = $this->_filter->filterHtmlEntities($value,$options['charset']);
                                break;
                            case 'Int':
                                $value = $this->_filter->filterInt($value);
                                break;
                            case 'NewLines':
                                $value = $this->_filter->filterNewLines($value);
                                break;
                            case 'StringLower':
                                $value = $this->_filter->filterStringLower($value);
                                break;
                            case 'StringUpper':
                                $value = $this->_filter->filterStringUpper($value);
                                break;
                            case 'Tags':
                                $value = $this->_filter->filterTags($value);
                                break;
                            case 'Trim':
                                $value = $this->_filter->filterTrim($value);
                                break;
                        }
                    }
                }
                return $value;
            } else {//$name doesnt exist
                return false;
            }
        }
        //$name == null
        return false;
    }

    public function getValues ()
    {
        $returnedValues = array();
        $elements = array_keys($this->_formElements);
        foreach ($elements as $name) {
            if ($this->_formElements[$name]['type'] != 'submit') {
                $returnedValues[$name] = $this->getValue($name);
            }
        }
        return $returnedValues;
    }

    /*
    * VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS
    * VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS
    * VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS VALIDATE FUNCTIONS
    */

    public function validateField($name=null,$customNames=false)
    {
        if ($name != null) {
            if (array_key_exists($name,$this->_formElements)) {
                $value = $this->getValue($name);

                if (is_array($this->_formElements[$name]['validate'])) {
                    $errors = '';

                    if ($this->_validate == null) {
                        $this->_validate = new Yasui_Validate($this->_lang);
                    }

                    foreach ($this->_formElements[$name]['validate'] as $validate => $options) {
                        switch ($validate) {
                            case 'Required':
                                if (!$this->_validate->validateRequired($value)) {
                                    if ($customNames) {
                                        $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                    }
                                    $errors .= $this->_validate->getError()."\n";
                                }
                            break;
                            case 'Alnum':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateAlnum($value,$options['whiteSpace'],$options['allAlphabets'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'Alpha':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateAlpha($value,$options['whiteSpace'],$options['allAlphabets'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'Digits':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateDigits($value,$options['whiteSpace'],$options['allAlphabets'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'Int':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateInt($value,$options['whiteSpace'],$options['allAlphabets'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'Between':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateBetween($value,$options['min'],$options['max'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'MoreThan':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateMoreThan($value,$options['limit'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'LessThan':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateLessThan($value,$options['limit'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'Email':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateEmail($value)) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'InArray':
                                if (!$this->_validate->validateInarray($value, $options['array'])) {
                                    if ($customNames) {
                                        $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                    }
                                    $errors .= $this->_validate->getError()."\n";
                                }
                                break;
                            case 'URL':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateURL($value)) {
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                            case 'RegExp':
                                if ($value != '' || array_key_exists('Required',$this->_formElements[$name]['validate'])) {
                                    if (!$this->_validate->validateRegExp($value,$options['regexp'])) {
                                        if ($customNames) {
                                            $errors .= 'Field '.$this->_formElements[$name]['label'].' ';
                                        }
                                        $errors .= $this->_validate->getError()."\n";
                                    }
                                }
                                break;
                        }
                        if ($this->_stopNotValid && $errors !== '') {
                            break;
                        }
                    }

                    if ($this->_stopNotValid) {
                        if ($errors !== '') {
                            $this->_error = $errors;
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        if ($errors !== '') {
                            $this->_error = $errors;
                            return false;
                        } else {
                            return true;
                        }
                    }
                } else {
                    return true;
                }
            } else {//$name doesnt exist
                return false;
            }
        }
        //$name == null
        return false;
    }

    public function validateForm($customNames=false)
    {
        $elements = array_keys($this->_formElements);
        $formValid = true;
        $formErrors = '';
        foreach ($elements as $element) {
            if ($formValid === true) {
                $formValid = $this->validateField($element,$customNames);
            } else {
                $this->validateField($element,$customNames);
            }

            if ($this->_stopNotValid && $formValid === false) {
                break;
            } else {
                $formErrors .= $this->_error;
            }
        }
        if ($formValid) {
            return true;
        } else {
            if ($formErrors != '') {
                $this->_error = $formErrors;
            }
            return false;
        }
    }

    public function getError()
    {
        return $this->_error;
    }

    /*
    * OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS
    * OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS
    * OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS OTHER FUNCTIONS
    */

    public function formSent()
    {
        if ($this->_formMethod === 'post') {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $submitButtons = $this->getSubmit();
                foreach ($submitButtons as $key) {
                    //VALUE OF SUBMIT BUTTON WAS SUBMITTED
                    if (isset($_POST[$key])) {
                        return true;
                    }
                }
                //NOT FOUND VALUE OF SUBMITTED BUTTON
                return false;
            } else {
                return false;
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $submitButtons = $this->getSubmit();
                foreach ($submitButtons as $key) {
                    //VALUE OF SUBMIT BUTTON WAS SUBMITTED
                    if (isset($_GET[$key])) {
                        return true;
                    }
                }
                //NOT FOUND VALUE OF SUBMITTED BUTTON
                return false;
            } else {
                return false;
            }
        }
    }

    private function getSubmit()
    {
        $submitButtons = array();
        foreach ($this->_formElements as $name => $element) {
            if ($element['type'] === 'submit' || $element['type'] === 'image') {
                $submitButtons[] = $name;
            }
        }
        return $submitButtons;
    }
}
