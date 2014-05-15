<?php

require_once('../Singleton/Singleton.php');

/**
 * Manage Session
 */
class FS_Session extends FS_Singleton
{
    const KEY_TIME = '___@@%%FS_Session_key_start%%@@___';
    
    private static $_timeout = 1800;//30 min
    
    private $_sessionTimeStart;
    
    /**
     * Construct
     * @param array $options    List of options:
     *                              - type (string): type of session (File cache, ...) ?
     */
    function __construct()
    {
        $lConfig = Fantasite::GetConfig(TRUE);
        
        if (isset($lConfig['session']['timeout']) && is_numeric($lConfig['session']['timeout'])) {
            self::$_timeout = $lConfig['session']['timeout'];
        }
    }
    
    /**
     * 
     * @return FS_Session
     */
    static public function GetInstance()
    {
        return parent::GetInstance();
    }
    
    /**
     * Set delay before session id is regenerated
     * @param int $timeout  Timeout in seconds.
     */
    static public function SetSessionTimeout($pTimeout) 
    {
        self::$_timeout = $pTimeout;
    }
    
    /**
     * Get delay before session id is regenerated
     * @return int
     */
    static public function GetSessionTimeout()
    {
        return self::$_timeout;
    }
    
    /**
     * Open session
     * @return \FS_Session
     */
    public function Start()
    {
        session_start();
        
        $currTime = time();
        $this->_sessionTimeStart = $this->Get(self::KEY_TIME, $currTime);
        $this->Remove(self::KEY_TIME);
        if ($this->_sessionTimeStart - $currTime >= self::$_timeout) {
            $this->_sessionTimeStart = $currTime;
            session_regenerate_id();
        }
        return $this;
    }
    
    /**
     * Close session
     * @return \FS_Session
     */
    public function Close()
    {
        $this->Set(self::KEY_TIME, $this->_sessionTimeStart);
        session_write_close();
        return $this;
    }
    
    /**
     * Reset Id session
     * @return \FS_Session
     */
    public function ResetId()
    {
        session_regenerate_id(TRUE);
        return $this;
    }
    
    /**
     * Reset all datas
     * @return \FS_Session
     */
    public function Reset()
    {
        session_unset();
        return $this;
    }
    
    /**
     * Check if object is stored in session
     * @param string $name  Name of object
     * @return Boolean
     */
    public function Has($pName)
    {
        return isset($_SESSION[$pName]);
    }
    
    /**
     * Set object to session
     * @param string $name  Name of object to store
     * @param mixed $value  Object to store
     * @return \FS_Session
     */
    public function Set($pName, $pValue)
    {
        $_SESSION[$pName] = $pValue;
        return $this;
    }
    
    /**
     * Get object from session
     * @param string $name  Name of object to get
     * @param mixed $default    If object doesn't exist, default value to return.
     * @return mixed
     */
    public function Get($pName, $pDefault = NULL)
    {
        if (isset($_SESSION[$pName])) {
            return $_SESSION[$pName];
        }
        return $pDefault;
    }
    
    /**
     * Remove object from session
     * @param string $name  Name of object to remove
     * @return \FS_Session
     */
    public function Remove($pName)
    {
        unset($_SESSION[$pName]);
        return $this;
    }
}

?>
