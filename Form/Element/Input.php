<?php

require_once('Element.php');

class FS_Form_Element_Input extends FS_Form_Element
{
    const TYPE_TEXT = 'text';
    const TYPE_RADIO = 'radio';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_IMAGE = 'image';
    
    private $_type;
    
    /**
     * Construct Input element
     * @param string $type
     * @param array $options
     */
    public function __construct($pType, array $pOptions = array())
    {
        parent::__construct();
        $this->_type = $pType;
    }
};