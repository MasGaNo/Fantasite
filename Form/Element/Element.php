<?php

require_once('../../Html/Html.php');

/**
 * Form element for each component of form
 * TODO:Validator, element type (input, textarea, ...), label, Options for each type: multiSelectOptions/Checked, Default value, filter StringTrim, require ...
 */
abstract class FS_Form_Element extends FS_Html
{
    private $_label;
    private $_element;
    
    private $_filters;
    private $_validators;
    
    private $_require;
    
    private static $_defaultOptions = array('label', 'name', 'validators', 'filters', 'value', 'require');
    private static $_nameFormCounter = array();
    
    /**
     * List of optionnal options
     * @var array
     */
    protected $_defaultProperty = array();
    
    /**
     * Construct Form Element
     * @param array $options
     */
    public function __construct(array $pOptions = array())
    {
        $this->_element = $this->getElementInstance($pOptions);
        
        $lDefaultOptions = array_merge(self::$_defaultOptions, $this->_defaultProperty);
        foreach ($pOptions AS $lKey => $lValue) {
            if (!in_array($lKey, $lDefaultOptions)) {
                continue;
            }
            $lMethod = 'Set' . ucfirst($lKey);
            $this->$lMethod($lValue);
        }
    }
    
    /**
     * Set label content
     * @param mixed $label Label content
     * @return \FS_Form_Element
     */
    public function SetLabel($pLabel)
    {
        if (!is_null($this->_label)) {
            $this->_label = new FS_Html_Label();
        }
        $this->_label->SetContent($pLabel);
        return $this;
    }
    
    /**
     * Set name of element
     * @param string $name Name of element
     * @return \FS_Form_Element
     */
    public function SetName($pName)
    {
        if (!isset(self::$_nameFormCounter[$pName])) {
            self::$_nameFormCounter[$pName] = 0;
            $lId = $pName;
        } else {
            self::$_nameFormCounter[$pName] += 1;
            $lId .= '-' . self::$_nameFormCounter[$pName];
        }
        $this->_element->SetName($pName)->SetId($lId);
        $this->_label->SetAttribute('for', $lId);
        return $this;
    }
    
    /**
     * Set value of element
     * @param string $pValue
     * @return \FS_Form_Element
     */
    public function SetValue($pValue)
    {
        $this->_element->SetAttribute('value', $pValue);
        return $this;
    }
    
    /**
     * Set validators
     * @param FS_Validate|array $validators Validators list. Override old validators.
     * @return \FS_Form_Element
     */
    public function SetValidators($pValidators)
    {
        if (!is_array($pValidators)) {
            $pValidators = array($pValidators);
        }
        foreach ($pValidators AS $lValidator) {
            if (!($lValidator instanceof FS_Validate)) {
                FS_Exception::Launch('One of validators is not a valid validator. Validator class must be an inheritance of FS_Validate.');
            }
        }
        $this->_validators = $pValidators;
        return $this;
    }
    
    /**
     * Set filters
     * @param FS_Filter|Array $filters Filters list. Override old filters
     * @return \FS_Form_Element
     */
    public function SetFilters($pFilters)
    {
        if (!is_array($pFilters)) {
            $pFilters = array($pFilters);
        }
        foreach ($pFilters AS $lFilter) {
            if (!($lFilter instanceof FS_Filter)) {
                FS_Exception::Launch('One of filters is not a valid filter. Filter class must be an inheritance of FS_Filter.');
            }
        }
        $this->_filters = $pFilters;
        return $this;
    }
    
    /**
     * Set if element is required
     * @param Boolean $require
     * @return \FS_Form_Element
     */
    public function SetRequire($pRequire)
    {
        $this->_require = $pRequire;
        return $this;
    }
    
    /**
     * Get if element is required
     * @return Boolean
     */
    public function GetRequire()
    {
        return $this->_require;
    }
    
    /**
     * Get instance of element
     * @param array $options
     * @return FS_Html Html element
     */
    abstract protected function getElementInstance(array &$pOptions = array());


    /**
     * Render Form Element
     * @return string
     */
    public function Render()
    {
        $lRender = '';
        if (!is_null($this->_label)) {
            $lRender .= $this->_label;
        }
        if (!is_null($this->_element)) {
            $lRender .= $this->_element;
        }
        return $lRender;
    }
    
    public function __toString()
    {
        return $this->Render();
    }
};
