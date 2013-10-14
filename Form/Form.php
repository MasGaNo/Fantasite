<?php

require_once('./Element/Element.php');

/**
 * Class to manage HTML form
 */
abstract class FS_Form
{
    private $_elements;
    private $_options;
    
    public function __construct($pOptions = NULL)
    {
        $this->_elements = array();
        $this->_options = $pOptions;
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
    
    public function Render()
    {
        
    }
};