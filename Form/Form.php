<?php

require_once('./Element/Element.php');

/**
 * Class to manage HTML form
 */
abstract class FS_Form extends FS_Html
{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';
    const METHOD_TRACE = 'trace';
    const METHOD_CONNECT = 'connect';
    const METHOD_PATCH = 'patch';
    
    const ENCTYPE_URLENCODE = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART = 'multipart/form-data';
    const ENCTYPE_TEXTPLAIN = 'text/plain';
    
    const ATTR_ACTION = 'action';
    const ATTR_METHOD = 'method';
    const ATTR_ENCTYPE = 'enctype';
    
    private $_elements;
    private $_setElements;
    private $_options;
    
    private $_errors;
    
    /**
     * Construct Form
     * @param mixed $options    Optionnal datas
     */
    public function __construct($pOptions = NULL)
    {
        parent::__construct('form', TRUE);
        $this->_elements = array();
        $this->_setElements = array();
        $this->_options = $pOptions;
        
        $this->_errors = array();
        
        $this->init();
    }
    
    /**
     * Get form's element
     * @param string $name
     * @return FS_Form_Element
     */
    public function __get($name)
    {
        if (isset($this->_setElements[$name])) {
            return $this->_setElements($name);
        }
        foreach ($this->_elements AS $lElement) {
            if ($lElement->GetName() === $name) {
                $this->$name = $lElement;
                return $lElement;
            }
        }
    }
    
    public function __set($pName, FS_Form_Element $pElement)
    {
        $this->_setElements[$pName] = $pElement;
    }
    
    /**
     * Initialize all elements for form.
     */
    abstract protected function init();
    
    /**
     * Add form element
     * @param FS_Form_Element $element
     * @param ...FS_Form_Element $element 
     * @return \FS_Form
     */
    public function AddElement(FS_Form_Element $pElement)
    {
        $lArgs = func_get_args();
        foreach ($lArgs AS $lKey => $lElement) {
            if (!($lElement instanceof FS_Form_Element)) {
                FS_Exception::Launch("Argument $lKey passed to FS_Form::AddElement() must be of the type FS_Form_Element, " . gettype($lElement) . ' given');
            }
            $this->_elements[] = $lElement;
        }
        return $this;
    }
    
    /**
     * Render all form
     * @return string
     */
    public function Render()
    {
        $this->SetContent(implode('', $this->_elements));
        return parent::Render();
    }
    
    /**
     * Set action of form
     * @param string $action    Action value.
     * @return \FS_Form
     */
    public function SetAction($pAction)
    {
        //$this->_action = $pAction;
        $this->AddAttribute(self::ATTR_ACTION, $pAction);
        return $this;
    }
    
    /**
     * Get action of form
     * @return string
     */
    public function GetAction()
    {
        return $this->GetAttribute(self::ATTR_ACTION);
    }
    
    /**
     * Set enctype of form
     * @param string $encType   Enctype value
     * @return \FS_Form
     */
    public function SetEncType($pEncType)
    {
        $this->AddAttribute(self::ATTR_ENCTYPE, $pEncType);
        return $this;
    }
    
    /**
     * Get enctype of form
     * @return string
     */
    public function GetEncType()
    {
        return $this->GetAttribute(self::ATTR_ENCTYPE);
    }
    
    /**
     * Set method of form
     * @param string $method    Method value
     * @return \FS_Form
     */
    public function SetMethod($pMethod)
    {
        $this->AddAttribute(self::ATTR_METHOD, $pMethod);
        return $this;
    }
    
    /**
     * Get method of form
     * @return string
     */
    public function GetMethod()
    {
        return $this->GetAttribute(self::ATTR_METHOD);
    }
    
    /**
     * Set name of form
     * @param string $name  Name value
     * @return \FS_Form
     */
    public function SetName($pName)
    {
        return parent::SetName($pName);
    }
    
    /**
     * Check is form is valid
     * @param array $values Form's values
     * @return boolean
     */
    public function IsValid(array $pValues)
    {
        $lIsValid = TRUE;
        foreach ($this->_elements AS $lElement) {
            if (isset($pValues[$lElement->GetName()])) {
                $lElement->SetValue($pValues[$lElement->GetName()]);
            }
            if ($lElement->IsValid() === FALSE) {
                $lIsValid = FALSE;
                $this->_errors[$lElement->GetName()] = $lElement->GetErrors();
            }
        }
        return $lIsValid;
    }
    
    /**
     * Get errors after validation.
     * @return array
     */
    public function GetErrors()
    {
        return $this->_errors;
    }
    
    /**
     * Get all values of form
     * @return array
     */
    public function GetValues()
    {
        $lValues = array();
        foreach ($this->_elements AS $lElement) {
            $lValues[$lElement->GetName()] = $lElement->GetValue();
        }
        return $lValues;
    }
    
    /**
     * Reset value
     */
    public function Reset()
    {
        foreach ($this->_elements AS $lElement) {
            $lElement->RemoveAttribute(FS_Html_Input::ATTR_VALUE);
        }
    }
    
    /**
     * Render all form
     * @return string
     */
    public function __toString()
    {
        return $this->Render();
    }
};