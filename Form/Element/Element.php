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
    private $_srcValue;
    private $_errors;
    
    private $_filters;
    private $_validators;
    
    private $_require;
    
    private static $_defaultOptions = array('label', 'name', 'validators', 'filters', 'require', 'value');
    private static $_nameFormCounter = array();
    
    /**
     * List of optionnal options
     * @var array
     */
    protected $_defaultProperty = array();
    
    static private $_defaultPartialView;
    private $_partialView;
    
    /**
     * Construct Form Element
     * @param string $name  Name of element
     * @param array $options
     */
    public function __construct($pName, array $pOptions = array())
    {
        $this->_element = $this->getElementInstance($pOptions);
        
        $this->_validators = array();
        $this->_filters = array();
        $this->_errors = array();
        
        $this->_partialView = self::$_defaultPartialView;
        
        $lDefaultOptions = array_merge(self::$_defaultOptions, $this->_defaultProperty);
        foreach ($pOptions AS $lKey => $lValue) {
            if (!in_array($lKey, $lDefaultOptions)) {
                continue;
            }
            $lMethod = 'Set' . ucfirst($lKey);
            $this->$lMethod($lValue);
        }
        $this->SetName($pName);
    }
    
    /**
     * Set default partial view for render all element
     * @param string $defaultPartial    Path of default partial view
     */
    static public function SetDefaultPartial($pDefaultPartial)
    {
        self::$_defaultPartialView = $pDefaultPartial;
    }
    
    /**
     * Set label content
     * @param mixed $label Label content
     * @return \FS_Form_Element
     */
    public function SetLabel($pLabel)
    {
        if (is_null($this->_label)) {
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
        if (!is_null($this->_label)) {
            $this->_label->AddAttribute('for', $lId);
        }
        return $this;
    }
    
    /**
     * Add attribute
     * @param string $name Name of attribute
     * @param string $value Value of attribute
     * @return \FS_Html
     */
    public function AddAttribute($pName, $pValue = '')
    {
        $this->_element->AddAttribute($pName, $pValue);
        return $this;
    }
    
    /**
     * Get attribute
     * @param string $name  Name of attribute
     * @return mixed
     */
    public function GetAttribute($pName)
    {
        return $this->_element->GetAttribute($pName);
    }
    
    /**
     * Remove attribute
     * @param string $name  Name of attribute to remove
     * @return \FS_Form_Element
     */
    public function RemoveAttribute($pName)
    {
        if ($pName === FS_Html_Input::ATTR_VALUE) {
            $this->_srcValue = NULL;
        }
        $this->_element->RemoveAttribute($pName);
        return $this;
    }
    
    /**
     * Set value of element
     * @param string $pValue
     * @return \FS_Form_Element
     */
    public function SetValue($pValue)
    {
        $this->_srcValue = $pValue;
        $pValue = $this->applyFilter($pValue);
        $this->_element->AddAttribute('value', $pValue);
        return $this;
    }
    
    /**
     * Get value of element
     * @return mixed
     */
    public function GetValue()
    {
        return $this->_element->GetAttribute('value');
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
        foreach ($pValidators AS &$lValidator) {
            if (!($lValidator instanceof FS_Validate)) {
                FS_Exception::Launch('One of validators is not a valid validator. Validator class must be an inheritance of FS_Validate. ' . get_class($lValidator) . ' given.');
            }
            $this->_validators[] = $lValidator;
        }
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
        foreach ($pFilters AS &$lFilter) {
            if (!($lFilter instanceof FS_Filter)) {
                FS_Exception::Launch('One of filters is not a valid filter. Filter class must be an inheritance of FS_Filter.');
            }
            $this->_filters[] = $lFilter;
        }
        if (!is_null($this->_srcValue)) {
            $this->SetValue($this->_srcValue);
        }
        return $this;
    }
    
    /**
     * Apply filter on value
     * @param mixed $value  Value to filter
     * @return mixed
     */
    private function applyFilter($pValue)
    {
        foreach ($this->_filters AS $lFilter) {
            $pValue = $lFilter->Filter($pValue);
        }
        return $pValue;
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
        if (is_null($this->_partialView)) {
            $lRender = '';
            if (!is_null($this->_label)) {
                $lRender .= $this->_label;
            }
            if (!is_null($this->_element)) {
                $lRender .= $this->_element;
            }
            return $lRender;
        } else {
            $lErrors = array();
            foreach ($this->_errors AS $lError) {
                $lErrors[] = array('error' => $lError);
            }
            
            $lView = new FS_View();
            $lView->SetScript($this->_partialView)
                    ->Assign('label', $this->_label)
                    ->Assign('element', $this->_element)
                    ->Assign('errors', $lErrors);
            return $lView->Render();
        }
    }
    
    public function __toString()
    {
        return $this->Render();
    }
    
    /**
     * Check if element is valid
     * @return boolean
     */
    public function IsValid()
    {
        $lValue = $this->_element->GetValue();

        if (is_null($lValue)) {
            if ($this->_require === TRUE) {
                $pError[] = FS_Translate::GetInstance()->Translate('_FORM_ELEMENT_EMPTY_ERROR');
                return FALSE;
            } 
            return TRUE;
        }
        
        $lIsValid = TRUE;
        foreach ($this->_validators AS $lValidator) {
            if ($lValidator->IsValid($lValue) === FALSE) {
                $this->_errors[] = $lValidator->GetError();
                $lIsValid = FALSE;
            }
        }
        return $lIsValid;
    }
    
    /**
     * Get errors after validation
     * @return type
     */
    public function GetErrors()
    {
        return $this->_errors;
    }
};
