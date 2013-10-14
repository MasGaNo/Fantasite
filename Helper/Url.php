<?php

/**
 * View Helper to generate Url
 *
 */
class FS_Helper_Url
{
    /**
     * Generate Url with Route
     * @param array|string $args   List of parameters or Route name
     * @param string $route   Route name
     * @param boolean $reset   Reset route parameters
     * @return string
     */
    public function Url($pArgs = null, $pRoute = '', $pReset = FALSE)
    {
        if (!empty($pRoute)) {
            if (is_string($pArgs)) {
                FS_Exception::Launch('First parameter must be an array.');
            } 
        } else if (is_null($pArgs)) {
            $pArgs = array();
        } else if (is_string($pArgs)) {
            $pRoute = $pArgs;
            $pArgs = array();
        }
        return FS_Router::GetInstance()->AssembleRoute($pRoute, $pArgs, $pReset);
    }
}
