<?php

require_once('./Element/Element.php');

/**
 * Class to manage HTML form
 */
abstract class FS_Form //extends FS_Html ?
{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    
    //const ENCTYPE_ = '';
    
    private $_elements;
    private $_setElements;
    private $_options;
    
    private $_action;
    private $_method;
    private $_enctype;
    private $_name;
    
    /**
     * Construct Form
     * @param mixed $options    Optionnal datas
     */
    public function __construct($pOptions = NULL)
    {
        $this->_elements = array();
        $this->_setElements = array();
        $this->_options = $pOptions;
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
        $this->_elements[$pName] = $pElement;
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
        return implode('', $this->_elements);
    }
    
    /**
     * Set action of form
     * @param string $action    Action value.
     * @return \FS_Form
     */
    public function SetAction($pAction)
    {
        $this->_action = $pAction;
        return $this;
    }
    
    /**
     * Get action of form
     * @return string
     */
    public function GetAction()
    {
        return $this->_action;
    }
    
    /**
     * Set enctype of form
     * @param string $encType   Enctype value
     * @return \FS_Form
     */
    public function SetEncType($pEncType)
    {
        $this->_enctype = $pEncType;
        return $this;
    }
    
    /**
     * Get enctype of form
     * @return string
     */
    public function GetEncType()
    {
        return $this->_enctype;
    }
    
    /**
     * Set method of form
     * @param string $method    Method value
     * @return \FS_Form
     */
    public function SetMethod($pMethod)
    {
        $this->_method = $pMethod;
        return $this;
    }
    
    /**
     * Get method of form
     * @return string
     */
    public function GetMethod()
    {
        return $this->_method;
    }
    
    /**
     * Set name of form
     * @param string $name  Name value
     * @return \FS_Form
     */
    public function SetName($pName)
    {
        $this->_name = $pName;
        return $this;
    }
    
    /**
     * Get value of form
     * @return string
     */
    public function GetName()
    {
        return $this->_name;
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