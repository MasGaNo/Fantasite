<?php

require_once('./Element/Element.php');

/**
 * Class to manage HTML form
 */
abstract class FS_Form
{
    private $_elements;
    private $_setElements;
    private $_options;
    
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
     * @param FS_Form_Element $pElement
     * @return \FS_Form
     */
    public function AddElement(FS_Form_Element $pElement)
    {
        $this->_elements[] = $pElement;
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
     * Render all form
     * @return string
     */
    public function __toString()
    {
        return $this->Render();
    }
};