<?php

require_once('Input.php');

class FS_Form_Element_Text extends FS_Form_Element_Input
{
    private $_type;
    
    /**
     * Construct Text element
     * @param array $options
     */
    public function __construct(array $pOptions = array())
    {
        parent::__construct(self::TYPE_TEXT, $pOptions);
    }
};