<?php

/**
 * Check if string is empty
 */
class FS_Validate_StringEmpty
{
    /**
     * Constructor
     * @param Boolean $validValue   If FALSE, Validator is not valid if the string is empty. Default: FALSE.
     */
    public function __constructor($pValidValue = FALSE)
    {
        parent::__constructor($pValidValue);
    }
    /**
     * Check if the string is empty
     * @param string $value Value to validate
     * @return boolean
     */
    public function IsValid($pValue)
    {
        if (empty($pValue) === $this->GetValidValue()) {
            $this->SetError(FS_Translate::GetInstance()->Translate('_FS_VALIDATE_STRING_EMPTY_ERROR'));
            return FALSE;
        }
        return TRUE;
    }
}
