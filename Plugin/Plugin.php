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
    const END = 'End';
    
    /**
     * Initialize the plugin. Call at the beginning of program.
     */
    public function Initialize()
    {
    }
    
    /**
     * Call before Controller is called
     * @return Boolean  If FALSE, stop execution of script and return buffer.
     */
    public function BeforeStart()
    {
        return TRUE;
    }
    
    /**
     * Call after Controller called and before View is called
     * @return Boolean  If FALSE, cancel render of view and return buffer.
     */
    public function BeforeRender()
    {
        return TRUE;
    }
    
    /**
     * Call after View called and before output of the buffer and end of program
     * @param string    $render Output of the page.
     */
    public function BeforeOutput($pRender)
    {
    }
    
    /**
     * Call at the end of program
     */
    public function End()
    {
    }
}

?>
