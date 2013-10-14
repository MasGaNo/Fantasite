<?php

/**
 * View helper to add modified date
 */
class FS_View_Helper_IncludedModifiedDate
{
    /**
     * Add modified date to file if options is active
     * @param string $filePath  File path to included
     * @param boolean $includedIgnorePath   If TRUE, add ignore path
     * @return string
     */
    public function IncludedModifiedDate($pFilePath, $pIncludedIgnorePath = TRUE)
    {
        $lConfig = Fantasite::GetConfig(TRUE);
        
        $lUrl = (($pIncludedIgnorePath === TRUE) ? FS_Router::GetInstance()->GetIgnorePath() : '') . $pFilePath;
        
        if ($lConfig['html']['includeModifiedDate']) {
            if (file_exists('.' . $lUrl)) {
                $lTime = filemtime('.' . $lUrl);
                $lPos = strrpos($lUrl, '.');
                if ($lPos === -1) {
                    $lPos = strlen($lUrl);
                }
                $lUrl = substr($lUrl, 0, $lPos) . '.' . $lTime . substr($lUrl, $lPos);
            }
        }
        return $lUrl;
    }
}