<?php

/**
 * Abstract class of Plugin
 */
abstract class FS_Plugin
{
    const INITIALIZE = 'Initialize';
    const BEFORE_START = 'BeforeStart';
    const BEFORE_RENDER = 'BeforeRender';
    const BEFORE_OUTPUT = 'BeforeOutput';
    
    /**
     * Initialize the plugin. Call at the beginning of program.
     */
    public function Initialize()
    {
    }
    
    /**
     * Call before Controller is called
     */
    public function BeforeStart()
    {
    }
    
    /**
     * Call after Controller called and before View is called
     */
    public function BeforeRender()
    {
    }
    
    /**
     * Call after View called and before output of the buffer and end of program
     * @param string    $render Output of the page.
     */
    public function BeforeOutput($pRender)
    {
    }
}

?>
