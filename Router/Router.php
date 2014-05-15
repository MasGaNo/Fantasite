<?php

require_once('../Singleton/Singleton.php');
require_once('../Config/Config.php');

require_once('Route.php');

/**
 *	Class to manage route
 */
class	FS_Router extends FS_Singleton
{
	private $_listRoute;
	private $_currRoute;
	private $_defaultRoute;
        private $_ignorePath = '';
	
	protected function __construct()
	{
		$this->_listRoute = array();
		$this->_defaultRoute = new FS_Route('FS_default', array(
												//'class' => 'FS_Route_Regex',
												'route' => '/:controller/:action/:param0/:param1/:param2/:param3/:param4/:param5/:param6/:param7/:param8/:param9',//TODO: Replace by Regex
												'controller' => 'Index',
												'action' => 'Index',
												'require' => array(
																'param0' => FALSE,
																'param1' => FALSE,
																'param2' => FALSE,
																'param3' => FALSE,
																'param4' => FALSE,
																'param5' => FALSE,
																'param6' => FALSE,
																'param7' => FALSE,
																'param8' => FALSE,
																'param9' => FALSE
															)
												));
	}
	
        /**
         * 
         * @return FS_Router
         */
        public static function GetInstance()
        {
            return parent::GetInstance();
        }
        
	/**
	 *	Add route file config
	 *	@param	string	$routesPath	Route file config path
	 *	@return	FS_Router
	 */
	public function AddRoutesFile($pRoutesPath)
	{
		$lFile = FS_Config::Parse($pRoutesPath);
		
		foreach ($lFile AS $lRoute => $lDatas) {
			$this->_listRoute[$lRoute] = new FS_Route($lRoute, $lDatas);
		}
		
		return $this;
	}
	
	/**
	 *	Check Url and set Route
	 *	@param	string	$url	Url rewrite
	 *	@return	FS_Router
	 */
	public function SetRoute($pUrl)
	{
            $pUrl = str_replace($this->_ignorePath, '', $pUrl);
            $lCleanUrl = explode('?', $pUrl);
            $lUri = explode('/', trim($lCleanUrl[0], '/'));
            foreach ($lUri AS $lKey => $lVal) {
                if (is_null($lVal) || $lVal === '') {
                    unset($lUri[$lKey]);
                }
            }
            $lUri = array_merge($lUri);
            
            $lFind = FALSE;
            foreach ($this->_listRoute AS $lRoute) {
                if ($lRoute->Match($lUri)) {
                    $this->_currRoute = $lRoute;
                    $lRoute->Execute($lUri);
                    $lFind = TRUE;
                    break;
                }
            }
            if ($lFind === FALSE) {
                if ($this->_defaultRoute->Match($lUri)) {
                    $this->_defaultRoute->Execute($lUri);
                }
            }
            return $this;
	}
        
        /**
         * Get ignore path
         * @return string
         */
        public function GetIgnorePath()
        {
            return $this->_ignorePath;
        }
        
        /**
         * Set part of path to ignore
         * @param string $ignorePath    Part of path
         * @return \FS_Router
         */
        public function SetIgnorePath($pIgnorePath)
        {
            $this->_ignorePath = $pIgnorePath;
            return $this;
        }
        
        /**
         * Generate Url from Route
         * @param string $routeName
         * @param array $parameters
         * @param boolean $reset
         * @return string
         */
        public function AssembleRoute($pRouteName, $pParameters, $pReset = FALSE)
        {
            if ($pRouteName === '') {
                return $this->_defaultRoute->Assemble($pParameters);
            }
            if ($pReset === FALSE) {
                $pParameters = array_merge($_GET, $pParameters);
            }
            return $this->_ignorePath . $this->_listRoute[$pRouteName]->Assemble($pParameters);
        }
};







