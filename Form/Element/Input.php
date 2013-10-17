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
        parent::__construct($pOptions);
        $this->_type = $pType;
    }
    
    /**
     * Get instance of element
     * @param array $options
     * @return FS_Html Html element
     */
    protected function getElementInstance(array &$pOptions = array())
    {
        return new FS_Html_Input($this->_type);
    }

};