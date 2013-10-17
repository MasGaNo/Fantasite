<?php

/**
 * Input Html element
 */
class FS_Html_Input extends FS_Html
{
    /**
     * Construct Input Html Element
     * @param string $type   Type of input
     */
    public function __construct($pType)
    {
        parent::__construct('input', FALSE);
    }
};
