<?php

/**
 * Compare string
 */
class FS_Validate_StringCompare
{
    private $_callable;
    
    /**
     * Constructor
     * @param string|callable $callable Value to compare or callable to get value to compare
     * @param Boolean $validValue   Value to compare if Validate is valid. Default: TRUE.
     */
    public function __constructor($pCallable, $pValidValue = TRUE)
    {
        $this->_callable = $pCallable;
        parent::__constructor($pValidValue);
    }
    /**
     * Check string is the same to value to compare
     * @param string $value Value to validate
     * @return boolean
     */
    public function IsValid($pValue)
    {
        if (is_string($this->_callable)) {
            $lStr = $this->_callable;
        } else if (is_array($this->_callable)) {
            $lProperty = $this->_callable[1];
            $lStr = $this->_callable[0]->$lProperty;
        }
        if (($lStr === $pValue) !== $this->GetValidValue()) {
            $this->SetError(FS_Translate::GetInstance()->Translate('_FS_VALIDATE_STRING_COMPARE_ERROR'));
            return FALSE;
        }
        return TRUE;
    }
}
