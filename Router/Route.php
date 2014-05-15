<?php

/**
 *	Class of basic Route
 */
class	FS_Route
{
    const MODULE = 'module';
    
	private $_name;
	private $_route;
	private $_datas;
	
	private $_parts;
	private $_partsParam;
	private $_minPartsCount;
	
	private $_isRewrite = false;
	
	/**
	 *	Constructor
	 *	@param	string	$name	Name of route
	 *	@param	array	$datas	Datas of route
	 */
	public function __construct($pName, $pDatas)
	{
		$this->_name = $pName;
		if (empty($pDatas['route'])) {
			FS_Exception::Launch('No route rule on the route [' . $pName . ']');
		}
		$this->_route = $pDatas['route'];
		unset($pDatas['route']);
		$this->_datas = $pDatas;
		$this->_minPartsCount = 0;
		
		$this->_parseRoute();
		// TODO: Parse 'require' rules to list which parameter is require even if there is no default value
		
		$lConfig = Fantasite::GetConfig(TRUE);
		if (isset($lConfig['route']['rewrite'])) {
			$this->_isRewrite = $lConfig['route']['rewrite'] === TRUE;
		}
	}
	
	/**
	 *	Parse route
	 *	@return FS_Route
	 */
	private function _parseRoute()
	{
            $lParts = explode('/', trim($this->_route, '/'));

            $this->_parts = array();
            $this->_partsParam = array();

            foreach ($lParts AS $lKey => $lVal) {
                $lPart = new stdClass();
                $lPart->match = TRUE;
                $lPart->require = TRUE;
                $lPart->name = $lVal;
                if ($lVal[0] !== ':') {
                    $this->_minPartsCount = $lKey;
                    $this->_parts[] = $lPart;
                } else {
                    $lVal = substr($lVal, 1);
                    $lPart->name = $lVal;
                    $lPart->match = FALSE;
                    if (!empty($this->_datas[$lVal])) {
                        $lPart->require = FALSE;
                        $lPart->default = $this->_datas[$lVal];
                    }
                    $this->_partsParam[$lVal] = $this->_parts[] = $lPart;
                }
            }

            if (isset($this->_datas['require']) && is_array($this->_datas['require'])) {
                foreach ($this->_datas['require'] AS $lKey => $lVal) {
                    if (!isset($this->_partsParam[$lKey])) {
                        $this->_partsParam[$lKey] = (object)array('match' => TRUE, 'name' => $lKey);
                        if (isset($this->_datas[$lKey])) {
                            $this->_partsParam[$lKey]->default = $this->_datas[$lKey];
                        }
                    }
                    $this->_partsParam[$lKey]->require = $lVal;
                }
            }

            return $this;
	}
	
	/**
	 *	Test if current url match with this Route
	 *	@param	Array|string	$url	URL's parts or full URL rewrite
	 *	@return Boolean
	 */
	public function Match($pUrl)
	{
            if (is_string($pUrl)) {
                $pUrl = explode('/', trim($pUrl, '/'));
            } else if (!is_array($pUrl)) {
                FS_Exception::Launch('Bad parameter for FS_Route::Match: expected array or string.');
            }

            if (isset($this->_partsParam[self::MODULE]) && $this->_partsParam[self::MODULE]->require === TRUE && $this->_partsParam[self::MODULE]->default !== FS_Request::GetInstance()->GetModule()) {
                return FALSE;
            }
            
            $lMax = count($pUrl);
            $lTotalMax = count($this->_parts);
            if ($lMax < $this->_minPartsCount || $lMax > $lTotalMax) {
                return FALSE;
            }

            $lDatas = array();
            for ($i = 0; $i < $lMax; ++$i) {
                $lValue = $pUrl[$i];
                $lParts = $this->_parts[$i];

                if ($lParts->match === TRUE) {
                    if (strcasecmp($lParts->name, $lValue) !== 0) {
                        return FALSE;
                    }
                } else {
                    $lDatas[$lParts->name] = $lValue;
                }
            }

            while ($i < $lTotalMax) {
                $lParts = $this->_parts[$i++];

                if ($lParts->require === TRUE) {
                    return FALSE;
                }
                if (property_exists($lParts, 'default')) {
                    $lDatas[$lParts->name] = $lParts->default;
                }
            }

            return TRUE;
	}
	
	/**
	 *	Load Request from Route parameters
	 *	@param	Array|string	$url	URL's parts or full URL rewrite
	 *	@return	FS_Route
	 */
	public function Execute($pUrl)
	{
            if (is_string($pUrl)) {
                $lCleanUrl = explode('?', $pUrl);
                $pUrl = explode('/', trim($lCleanUrl[0], '/'));
                foreach ($pUrl AS $lKey => $lUrl) {
                    if (is_null($lUrl) || $lUrl === '') {
                        unset($pUrl[$lKey]);
                    }
                }
                $pUrl = array_merge($pUrl);
            } else if (!is_array($pUrl)) {
                FS_Exception::Launch('Bad parameter for FS_Route::Match: expected array or string.');
            }

            //If route doesn't have controller or action in url path, set default controller and action
            foreach (array('controller', 'action') AS $lParam) {
                if (isset($this->_datas[$lParam])) {
                    //$_GET[$lParam] = $this->_datas[$lParam];
                    $lMethod = 'Set' . ucfirst($lParam);
                    FS_Request::GetInstance()->$lMethod($this->_datas[$lParam]);
                }
            }
            
            $lInd = 0;
            $lMax = count($pUrl);
            $lTotalMax = count($this->_parts);

            for ($i = 0; $i < $lMax; ++$i) {
                $lParts = $this->_parts[$i];
                if ($lParts->match === TRUE) {
                    $_GET['param' . $lInd++] = $pUrl[$i];
                } else {
                    $_GET[$lParts->name] = $pUrl[$i];
                }
            }
            while ($i < $lTotalMax) {
                $lParts = $this->_parts[$i++];
                if (property_exists($lParts, 'default')) {
                    $_GET[$lParts->name] = $lParts->default;
                }
            }

            if (isset($_GET['action'])) {
                FS_Request::GetInstance()->SetAction($_GET['action']);
            }
            if (isset($_GET['controller'])) {
                FS_Request::GetInstance()->SetController($_GET['controller']);
            }
            
            if (isset($this->_datas['module'])) {
                $_SERVER['APPLICATION_MODULE'] = $this->_datas['module'];
            }
            
            return $this;
	}
	
	/**
	 *	Assemble and generate URL
	 *	@param	Array	$datas	Datas parameters for url
	 *	@return	string
	 */
	public function	Assemble($pDatas)
	{
            $lUri = array();
            $i = 0;
            $pDatas = array_merge($this->_datas, $pDatas);
            foreach ($this->_parts AS $lParts) {
                if ($lParts->match === TRUE) {
                    $lUri[] = array('name' => 'param' . $i++, 'value' => $lParts->name);
                } else {
                    if ($lParts->require === TRUE) {
                        if (!isset($pDatas[$lParts->name])) {
                            FS_Exception::Launch($lParts->name . ' is missing for ' . $this->_name . ' route assemble.');
                        }
                        $lUri[] = array('name' => $lParts->name, 'value'=> $pDatas[$lParts->name]);
                    } else if (isset($pDatas[$lParts->name]) && !is_null($pDatas[$lParts->name])) {
                        $lUri[] = array('name' => $lParts->name, 'value' => $pDatas[$lParts->name]);
                    } else if (property_exists($lParts, 'default') && !is_null($lParts->default)) {
                        $lUri[] = array('name' => $lParts->name, 'value' => $lParts->default);
                    } else {
                        //FS_Exception::Launch($lParts->name . ' is missing for ' . $this->_name . ' route assemble.');
                    }
                }
            }
            if ($this->_isRewrite === TRUE) {
                return $this->_generateRewriteUri($this->_cleanUri($lUri));
            }
            return $this->_generateBasicUri($lUri);
	}
	
        /**
         * Clean Uri and remove default parameters
         * @param array $uri    Uri data to clean
         * @return array
         */
        private function _cleanUri(Array &$pUri)
        {
            $pUri = array_reverse($pUri);
            foreach ($pUri AS $lKey => $lValue) {
                $lPart = $this->_partsParam[$lValue['name']];
                if ($lPart->require === FALSE && property_exists($lPart, 'default') && strcasecmp($lPart->default, $lValue['value']) === 0) {
                    unset($pUri[$lKey]);
                } else {
                    break;
                }
            }
            return array_reverse($pUri);
        }
        
	/**
	 *	Generate rewrite url
	 *	@param	Array	$uri	List of parts uri
	 *	@return	String
	 */
	private function _generateRewriteUri(Array $pUri)
	{
            $lUri = array();
            foreach ($pUri AS $lValue) {
                /*if (($lValue['name'] === 'controller' || $lValue['name'] === 'action') && $lValue['value'] === 'Index') {
                    continue;
                }*/
                $lUri[] = $lValue['value'];
            }
            return count($lUri) ? '/' . implode('/', $lUri) : '/';
	}
	
	/**
	 *	Generate basic url
	 *	@param	Array	$uri	List of parts uri
	 *	@return	String
	 */
	private function _generateBasicUri(Array $pUri)
	{
            $lUri = array();
            foreach ($pUri AS $lValue) {
                /*if (($lValue['name'] === 'controller' || $lValue['name'] === 'action') && $lValue['value'] === 'Index') {
                    continue;
                }*/
                $lUri[] = $lValue['name'] . '=' . $lValue['value'];
            }
            
            return count($lUri) ? '?' . implode('&', $lUri) : '';
	}
};




