<?php

/**
 * Check string length
 */
class FS_Validate_StringMinMax
{
    private $_min;
    private $_max;
    
    /**
     * Constructor
     * @param int $min  Min length of character. Default: 0
     * @param int $max  Max length of character. Default: Infinite
     */
    public function __constructor($pMin = 0, $pMax = 9999999999)
    {
        $this->_min = $pMin;
        $this->_max = $pMax;
        parent::__constructor(TRUE);
    }
    /**
     * Check string length is include between min and max
     * @param string $value Value to validate
     * @return boolean
     */
    public function IsValid($pValue)
    {
        $lLen = strlen($pValue);
        if ($lLen < $this->_min || $lLen > $this->_max) {
            $this->SetError(FS_Translate::GetInstance()->Translate('_FS_VALIDATE_STRING_MINMAX_ERROR'));
            return FALSE;
        }
        return TRUE;
    }
}
