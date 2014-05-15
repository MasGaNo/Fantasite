<?php

/**
 * Plugin to manage Session
 */
class FS_Plugin_Session extends FS_Plugin
{
    public function Initialize()
    {
        FS_Session::GetInstance()->Start();
    }
    
    public function End()
    {
        FS_Session::GetInstance()->Close();
    }
}

?>
