<?php

/**
 * Force redirect
 *
 */
class FS_Controller_Helper_Redirect
{
    /**
     * Force redirection
     * @param string $url   Url to redirect the user
     * @param int $code     HTTP Request code
     */
    public function Redirect($pUrl, $pCode = NULL)
    {
        header('Location: ' . $pUrl, TRUE, $pCode);
        die;
    }
}

?>
