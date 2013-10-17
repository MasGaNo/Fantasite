<?php

/**
 * Check if string is valid email
 */
class FS_Validate_Email
{
    /**
     * Check if the string is valid email
     * @param string $value Value to validate
     * @return boolean
     */
    public function IsValid($pValue)
    {
        if (preg_match('#^[a-z0-9_.-]+@[a-z0-9_-]+(\.[a-z0-9_-]{1,})*\.[a-z]{2,4}$#i', $pValue) === $this->GetValidValue()) {
            $this->SetError(FS_Translate::GetInstance()->Translate('_FS_VALIDATE_EMAIL_ERROR'));
            return FALSE;
        }
        return TRUE;
    }
}
