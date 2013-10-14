<?php

require_once('Element.php');

class FS_Form_Element_Input extends FS_Form_Element
{
    private $_type;
    
    public function __construct($pType)
    {
        $this->_type = $pType;
    }
    
    public function Render()
    {
    }
};