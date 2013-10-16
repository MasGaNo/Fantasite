<?php

require_once('../../Html/Html.php');

/**
 * Form element for each component of form
 * TODO:Validator, element type (input, textarea, ...), label, Options for each type: multiSelectOptions/Checked, Default value, filter StringTrim, ...
 */
abstract class FS_Form_Element extends FS_Html
{
    private $_label;
    private $_element;
    
    /**
     * Construct Form Element
     */
    public function __construct()
    {
    }
    
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
