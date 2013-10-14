<?php

/***
 *	Class to parse config file
 */
class	FS_Config
{
    private static $_root;
    private static $_key;
	/***
	 *	Parse config file
	 *	@param	string	path	File path to load
	 *	@return array	Config object.
	 */
	static public function Parse($pPath)
	{
		return self::loadConfigFile($pPath);
	}
	
	static private function loadConfigFile($pConfig)
	{
		$lConfs = file_get_contents($pConfig);
		self::$_root = $lConfig = $lGlobalConfig = array();
		$lEnv = null;
		
		foreach (explode("\n", $lConfs) AS $lConf) {
			$lConf = trim($lConf);

			if (preg_match('#^\[(.*)\]$#', $lConf)) {
				if (is_null($lEnv))
					$lGlobalConfig = $lConfig;
				else
					$lGlobalConfig[$lEnv] = $lConfig;
				
				$lConfig = array();
                                self::$_root = array();
				$lConf = explode(':', substr($lConf, 1, -1));
				$lMax = count($lConf);
				for ($i = 1; $i < $lMax; ++$i) {
					self::copyArray($lGlobalConfig[trim($lConf[$i])], $lConfig);
				}
				$lEnv = trim($lConf[0]);
				//$lGlobalConfig[$lEnv] = $lConfig;
			} else if (!empty($lConf) && $lConf[0] !== ';') {
				$lConf = explode('=', $lConf);
                                self::$_key = trim($lConf[0]);
				$lConf[0] = explode('.', $lConf[0]);
				$lConf[1] = trim($lConf[1]);
				self::recurSetValue($lConfig, $lConf[0], $lConf[1]);
			}
		}
		if (is_null($lEnv))
			$lGlobalConfig = $lConfig;
		else
			$lGlobalConfig[$lEnv] = $lConfig;
		return $lGlobalConfig;
	}
	
	static private function	copyArray(array &$pSrc, array &$pDest)
	{
		foreach ($pSrc AS $lKey => $lValue) {
			if (is_array($lValue)) {
				$lDest = array();
				self::copyArray($lValue, $lDest);
				$pDest[$lKey] = $lDest;
			} else {
				$pDest[$lKey] = $lValue;
			}
		}
	}
	
	static private function recurSetValue(array &$pConfig, array &$pKeys, &$pValue)
	{
		$lKey = array_shift($pKeys);
		if (count($pKeys) === 0) {
                    $lValue;
                    if (is_numeric($pValue))
                        $lValue = $pValue + 0;
                    else if ($pValue === 'false' || $pValue === 'true')
                        $lValue = $pValue === 'true';
                    else
                        $lValue = self::checkVarToString(self::parseString($pValue));
                    self::$_root[self::$_key] = $pConfig[trim($lKey)] = $lValue;
                    return;
		}
		if (!isset($pConfig[$lKey]))
			$pConfig[trim($lKey)] = array();
		self::recurSetValue($pConfig[$lKey], $pKeys, $pValue);
	}
	
	static private function parseString($pStr)
	{
		if ($pStr[0] === $pStr[strlen($pStr) - 1] && ($pStr[0] === '"' || $pStr[0] === "'"))
			return substr($pStr, 1, -1);
		return $pStr;
	}
        
        static private function checkVarToString($pStr)
        {
            return preg_replace_callback('#%(.+)%#isU', array('self', 'toVar'), $pStr);
        }
        
        static private function toVar($pMatch)
        {
            if (!isset(self::$_root[$pMatch[1]])) {
                FS_Exception::Launch($pMatch[0] . ' is undefined in config file.');
            }
            
            return self::$_root[$pMatch[1]];
        }
};




