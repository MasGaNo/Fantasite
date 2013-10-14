<?php

/**
 * Manager of file loader 
 */
class FS_Loader
{
    public static function AutoLoad($pPath, $pRecursion = FALSE)
    {
        $lFd = opendir($pPath);
        if ($lFd === FALSE) {
            FS_Exception::Launch('Can not open ' . $pPath);
        }
        while ($lFile = readdir($lFd)) {
            if (preg_match('#\.php$#', $lFile)) {
                include_once($pPath . '/' . $lFile);
            } else if ($pRecursion === TRUE && is_dir($pPath . '/' . $lFile)) {
                self::AutoLoad($pPath . '/' . $lFile, TRUE);
            }
        }
    }
}

?>
