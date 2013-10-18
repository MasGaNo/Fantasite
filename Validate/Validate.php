<?php

/**
 * Validate controle (FS_Form, ...)
 */
abstract class FS_Validate
{
    private $_error;
    private $_validValue;
    
    /**
     * Constructor
     * @param Boolean $validValue   Value to compare if Validate is valid. Default: TRUE.
     */
    public function __construct($pValidValue = TRUE)
    {
        $this->_validValue = $pValidValue;
    }
    
    /**
     * Check if the value is valid
     * @param mixed $value Value to validate
     * @return boolean
     */
    abstract public function IsValid($pValue);
    
    /**
     * Set errors
     * @param string|array $error
     * @return \FS_Validate
     */
    protected function SetError($pError)
    {
        $this->_error = $pError;
        return $this;
    }
    
    /**
     * Get error message.
     * @return string|array
     */
    public function GetError()
    {
        return $this->_error;
    }
    
    /**
     * Get valid value
     * @return Boolean
     */
    protected function GetValidValue()
    {
        return $this->_validValue;
    }
}
