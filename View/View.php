<?php

/**
 *	Class of rendering view
 */
class	FS_View
{
	private $_script;
	private $_module;

	private $_saveScript;
	private $_saveModule;
        
        static private $_includedPaths = array();
        private $_helperList = array();
	
	public function __construct()
	{
		
	}
	
	public function __get($pKey)
	{
		FS_Exception::Launch('Key ' . $pKey . ' does not exist.');
		return null;
	}
	
	public function __set($pKey, $pVal)
    {
        if ('_' != substr($pKey, 0, 1)) {
            $this->$pKey = $pVal;
            return;
		}
		FS_Exception('Setting private or protected class members is not allowed: ' . $pKey);
	}
	
	public function __isset($pKey)
    {
        if ('_' != substr($pKey, 0, 1)) {
            return isset($this->$pKey);
        }

        return false;
    }
	
	public function __unset($pKey)
    {
        if ('_' != substr($pKey, 0, 1) && isset($this->$pKey)) {
            unset($this->$pKey);
        }
    }
    
    public function __call($name, $arguments)
    {
        if (!isset($this->_helperList[$name])) {
            $lName = ucfirst($name);
            $lFound = FALSE;

            foreach (self::$_includedPaths AS $lPath => $lVal) {
                $lClassName = str_replace('/', '_', (!is_null($lVal) ? $lVal : $lPath) . $lName);
            
                if (class_exists($lClassName, false)) {
                    $lFound = TRUE;
                    break;
                }
                else if (file_exists($lPath . $lName . '.php')) {
                    include_once($lPath . $lName . '.php');
                    if (class_exists($lClassName, false)) {
                        $lFound = TRUE;
                        break;
                    }
                }
            }
            if ($lFound === FALSE) {
                FS_Exception::Launch('Helper "' . $name . '" was not found.');
            }
            $lClass = new $lClassName();
            $this->_helperList[$name] = $lClass;
            if (method_exists($lClass, 'SetView')) {
                $lClass->SetView($this);
            }
        }

        return call_user_func_array(array($this->_helperList[$name], $name), $arguments);
    }
	
	/**
	 *	Assign a script to view
	 *	@param	string	$script	Script path to render
	 *	@param	string	$module	If provided, change module to render path
	 *	@return FS_View
	 */
	public function SetScript($pScript, $pModule = NULL)
	{
		$this->_script = $pScript;
		$this->_module = $pModule;
		return $this;
	}
	
	/**
	 *	Return if a script is assigned
	 *	@return bool
	 */
	public function HasScript()
	{
		return !empty($this->_script);
	}
	
	/**
	 *	Assign datas to view
	 *	@param array|object|string	$datas	Datas collections to assign
	 *	@param	mixed	$value	Value to assign. If not null, $datas has to be a string.
	 *	@return FS_View
	 */
	public function Assign($pDatas, $pValue = NULL)
	{
		if (is_string($pDatas)) {
			if (substr($pDatas, 0, 1) === '_') {
				FS_Exception::Launch('Setting private or protected class members is not allowed: ' . $pDatas);
			}
			$this->$pDatas = $pValue;
		}
		else if (is_array($pDatas)) {
			foreach ($pDatas AS $lKey => $lValue) {
				if (substr($lKey, 0, 1) === '_') {
					FS_Exception::Launch('Setting private or protected class members is not allowed: ' . $lKey);
				}
				$this->$lKey = $lValue;
			}
		} else if (is_object($pDatas)) {
			$lDatas = get_object_vars($pDatas);
			foreach ($lDatas AS $lKey => $lValue) {
				if (substr($lKey, 0, 1) !== '_') {
					$this->$lKey = $lValue;
				}
			}
		} else {
			FS_Exception::Launch('Datas is not an array or an object.');
		}
		return $this;
	}
	
	/**
	 *	Render script
	 *	@return string
	 */
	public function Render()
	{
		ob_start();
                foreach (array('', '/' . Fantasite::SCRIPTS . '/' . FS_Request::GetInstance()->GetAction(), '/' . Fantasite::SCRIPTS, '/' . Fantasite::LAYOUTS) AS $lSubDirectory) {
                    $lFile = APPLICATION_PATH . Fantasite::MODULES . 
                                            '/' . (isset($this->_module) ? $this->_module : FS_Request::GetInstance()->GetModule()) . //Default ?
                                            '/' . Fantasite::VIEWS .
                                            $lSubDirectory .
                                            '/' . $this->_script;
                    if (file_exists($lFile)) {
                        break;
                    }
                }
		include($lFile);
		return ob_get_clean();
	}
	
	/**
	 *	Get all assigned variables
	 *	@return	array
	 */
	public function GetVars()
        {
            $lVars = get_object_vars($this);
            foreach ($lVars as $lKey => $lValue) {
                if ('_' == substr($lKey, 0, 1)) {
                    unset($lVars[$lKey]);
                }
            }

            return $lVars;
        }
	
	/**
	 * 	Clear all assigned variables
	 *	@return FS_View
	 */
	public function ClearVars()
    {
        $lVars   = get_object_vars($this);
        foreach ($lVars as $lKey => $lValue) {
            if ('_' != substr($lKey, 0, 1)) {
                unset($this->$lKey);
            }
        }
		return $this;
    }
	
	/**
	 *	Render sub partial script
	 *	@param	string	$script	Path of partial view
	 *	@param	array|object	$datas	Datas to assign to the view
	 *	@return	string
	 */
	public function Partial($pScript, $pDatas = NULL)
	{
		$lView = new FS_View();
		if (isset($this->partialCounter)) {
			$lView->Assign('partialCounter', $this->partialCounter);
		}
		if (isset($this->partialTotalCount)) {
			$lView->Assign('partialTotalCount', $this->partialCounter);
		}
		$lView->SetScript($pScript);
                if (!is_null($pDatas)) {
                    $lView->Assign($pDatas);
                }
                return $lView->Render();
	}
	
	/**
	 *	Render sub partial script for each datas in datas collection
	 *	@param	string $script	Path of partial view
	 *	@param	array|object	$datasIterator	List of datas to assign for each render view
	 *	@param	array|object	$globalDatas	Datas to assign for all render view
	 *	@return string
	 */
	public function PartialLoop($pScript, $pDatasIterator, $pGlobalDatas = NULL)
	{
		$lView = new FS_View();
		$lView->SetScript($pScript);
		$lTotal = count($pDatasIterator);
		$lCurr = 0;
		$lContent = '';
		foreach ($pDatasIterator AS $lDatas) {
			$lView->ClearVars();
			if (!is_null($pGlobalDatas))
				$lView->Assign($pGlobalDatas);
			$lView->Assign($lDatas)->Assign('partialCounter', $lCurr++)->Assign('partialTotalCount', $lTotal);
			$lContent .= $lView->Render();
		}
		return $lContent;
	}
	
	/**
	 *	Save and reset current script and module.
	 *	@return FS_View
	 */
	protected function saveScript()
	{
		$this->_saveScript = $this->_script;
		$this->_saveModule = $this->_module;
		$this->_script = $this->_module = NULL;
		return $this;
	}
	
	/**
	 *	Reload script and module previously save.
	 *	@return FS_View
	 */
	protected function reloadScript()
	{
		$this->_script = $this->_saveScript;
		$this->_module = $this->_saveModule;
		$this->_saveScript = $this->_saveModule = NULL;
		return $this;
	}

        /**
         * Add path to include for Controller Helper
         * @param string $path  Path to include
         * @param string $alias Alias of the path. If NULL, the path is use for class name resolution.
         */
        public static function AddIncludedPath($pPath, $pAlias = null)
        {
            self::$_includedPaths[$pPath] = $pAlias;
        }
        
        /**
         * Remove path to include for Controller Helper
         * @param string $path  Path to remove
         */
        public static function RemoveIncludedPath($pPath)
        {
            unset(self::$_includedPaths[$pPath]);
        }
}
