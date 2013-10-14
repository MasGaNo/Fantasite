<?php

include_once('../Singleton/Singleton.php');

class	FS_Url extends FS_Singleton
{
	private $_host;
	private $_extension;
	private $_subDomain;
	private $_domain;
	private $_port;
	private $_protocole;
	private $_url;
	private $_page;
	
	private $_getList = array(
							'host'		=>	'_host',
							'domain'	=>	'_domain',
							'port'		=>	'port',
							'protocole'	=>	'_protocole',
							'scheme'	=>	'_protocole',
							'extension'	=>	'_extension',
							'subDomain'	=>	'_subDomain',
							'url'		=>	'_url',
							'page'		=>	'_page',
							'script'	=>	'_page',
						);
	
	protected function __construct()
	{
		$this->_parseHost();
	}
        
        /**
         * 
         * @return FS_Url
         */
        public static function GetInstance()
        {
            return parent::GetInstance();
        }
	
	public function __get($pValue)
	{
		$lVal = isset($this->_getList[$pValue]) ? $this->_getList[$pValue] : null;
		return !is_null($lVal) ? $this->$lVal : null;
	}
	
	private function _parseHost()
	{
		/*
		$lUrl = 'http://fr.www.closeworld.com:8080/';
		//$lUrl = 'http://localhost:8080/';
		
		//var_dump(preg_match('#^(([a-z]*)://)+([a-z0-9_-]{1,}\.)*([a-z0-9_-]{1,})(\.[a-z0-9]{2,4})?(\:[0-9]{1,})?(\/(.+))?$#', $lUrl));
		preg_match_all('#^(([a-z]*)://)+([a-z0-9_-]{1,}\.)*([a-z0-9_-]{1,}){1}(\.[a-z0-9]{2,4})?(\:([0-9]{1,}))?#', $lUrl, $lArray);//reverse regex ?
		var_dump($lArray);
		*/
		$this->_domain = $_SERVER['SERVER_NAME'];
                $this->_port = $_SERVER['SERVER_PORT'];
                $this->_protocole = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';//$_SERVER['REQUEST_SCHEME'];
                $this->_page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'];
		
                if (!is_null($this->_domain) && !$this->_isIp($this->_domain)) {
			$lPart = explode('.', $this->_domain);
			if (count($lPart) === 1) {
				$this->_extension = $this->_subDomain = '';
				$this->_host = $lPart[0];
			} else {
				$this->_extension = array_pop($lPart);
				$this->_host = array_pop($lPart);
				$this->_subDomain = implode('.', $lPart);//TODO:Manage if is ip adress or localhost. Fallback default www module.
			}
		}
		
		$this->_url = $this->_protocole . '://' . $this->_domain . (($this->_port !== 80) ? ':' . $this->_port : '') . $this->_page;
	}
        
        /**
         * Check if domain is an ip adress
         * @param string $domain    Domain to check
         * @return boolean          TRUE if is ip adress
         */
        private function _isIp($pDomain)
        {
            $lParts = explode('.', $pDomain);
            $lCount = count($lParts);
            if ($lCount !== 4 && $lCount !== 6) {//IPv4 && IPv6 ?
                return FALSE;
            }
            foreach ($lParts AS $lPart) {
                if (!is_numeric($lPart) || $lPart < 0 || $lPart > 255) {
                    return FALSE;
                }
            }
            return TRUE;
        }
}
