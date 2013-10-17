<?php

require_once('Input.php');

class FS_Form_Element_Password extends FS_Form_Element_Input
{
    /**
     * Construct Text element
     * @param array $options
     */
    public function __construct(array $pOptions = array())
    {
        parent::__construct(self::TYPE_PASSWORD, $pOptions);
    }
};