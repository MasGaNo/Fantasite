<?php

require_once('Element.php');

class FS_Form_Element_Input extends FS_Form_Element
{
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_IMAGE = 'image';
    const TYPE_PASSWORD = 'password';
    const TYPE_RADIO = 'radio';
    const TYPE_SUBMIT = 'submit';
    const TYPE_TEXT = 'text';
    
    private $_type;
    
    /**
     * Construct Input element
     * @param string $name  Name of element
     * @param string $type
     * @param array $options
     */
    public function __construct($pName, $pType, array $pOptions = array())
    {
        $this->_type = $pType;
        parent::__construct($pName, $pOptions);
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