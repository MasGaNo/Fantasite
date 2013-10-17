<?php

/**
 * Trim whitespace of string
 */
class FS_Filter_StringTrim extends FS_Filter
{
    private $_valueToTrim;
    
    /**
     * Constructor
     * @param string $valueToTrim   By default, this filter trim whitespace. But characters list can be redefined by passing string list of characters.
     */
    public function __construct($pValueToTrim = NULL)
    {
        $this->_valueToTrim = $pValueToTrim;
    }
    
    /**
     * Remove whitespace after and before string value
     * @param string $value Value to filter
     * @return string
     */
    public function Filter($pValue)
    {
        if (is_null($this->_valueToTrim)) {
            return trim($pValue);
        }
        return trim($pValue, $this->_valueToTrim);
    }
}

?>
