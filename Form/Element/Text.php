<?php

require_once('Input.php');

class FS_Form_Element_Text extends FS_Form_Element_Input
{
    /**
     * Construct Text element
     * @param string $name  Name of element
     * @param array $options
     */
    public function __construct($pName, array $pOptions = array())
    {
        parent::__construct($pName, self::TYPE_TEXT, $pOptions);
    }
};