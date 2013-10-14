<?php

class	Fantasite
{
	const PRODUCTION = 'production';
	const STAGING = 'staging';
	const TESTING = 'testing';
	const DEVELOPMENT = 'development';
	
	const MODULES = 'modules';
	const CONTROLLERS = 'controllers';
	const VIEWS = 'views';
	const LAYOUTS = 'layouts';
	const SCRIPTS = 'scripts';
	
        private static $_instance = NULL;
        
        public static function GetInstance()
        {
            if (is_null(self::$_instance)) {
                new Fantasite();
            }
            return self::$_instance;
        }
        
	private static $_config = NULL;
        
        private $_pluginList;
        
	/**
	 *  Start initialisation of Fantasite Framework.
	 *  @param	string	$configFile	Load file configuration
         *  @param  Boolean $launchProgram  If TRUE, execute the program.
	 */
        public function __construct($pConfigFile = null, $pLaunchProgram = TRUE)
        {
            if (!is_null(self::$_instance)) {
                FS_Exception::Launch('Attempt to instantiate more than 1 instance of Fantasite.');
            }
            
            self::$_instance = $this;
            
            $this->_pluginList = array();

            if (!defined('APPLICATION_ENV')) {
                define('APPLICATION_ENV', Fantasite::PRODUCTION);
            }
            if (!defined('APPLICATION_PATH')) {
                define('APPLICATION_PATH', './');
            }
            if (!defined('FANTASITE_PATH')) {
                define('FANTASITE_PATH', './Fantasite/');
            }

            $this->loadModules();

            if (!is_null($pConfigFile))
                    self::$_config = FS_Config::Parse($pConfigFile);

            if (!is_null(self::$_config[APPLICATION_ENV]['route']['defaultConfig'])) {
                    FS_Router::GetInstance()->AddRoutesFile(APPLICATION_PATH . 'config/' . self::$_config[APPLICATION_ENV]['route']['defaultConfig']);
            }

            if (is_dir(FANTASITE_PATH . 'Controller/Helper/')) {
                FS_Controller::AddIncludedPath(FANTASITE_PATH . 'Controller/Helper/', 'FS/Controller/Helper/');
            }

            if (is_dir(FANTASITE_PATH . 'View/Helper/')) {
                FS_View::AddIncludedPath(FANTASITE_PATH . 'View/Helper/', 'FS/View/Helper/');
            }
            
            if (is_dir(FANTASITE_PATH . 'Helper/')) {
                FS_Controller::AddIncludedPath(FANTASITE_PATH . 'Helper/', 'FS/Helper/');
                FS_View::AddIncludedPath(FANTASITE_PATH . 'Helper/', 'FS/Helper/');
            }
            
            FS_Db::GetInstance();//Hack because of mysql_real_escape_string depend of mysql_connect, so, until we use PDO_MySQL to protect request and avoid use of mysql_real_escape_string, do this...

            if ($pLaunchProgram === TRUE) {
                $this->LaunchProgram();
            }
	}
	
	/**
	 *	Return Config object from file configuration
	 *	@param	bool	$envConfig	If True, return only configuration for the current environment
	 *	@return	Array
	 */
	static public function GetConfig($pEnvConfig = FALSE)
	{
		if ($pEnvConfig)
			return self::$_config[APPLICATION_ENV];
		return self::$_config;
	}
        
        /**
         * Add new Plugin to program.
         * @param FS_Plugin $plugin Instance of Plugin to add
         * @return Fantasite
         */
        public function AddPlugin(FS_Plugin $pPlugin)
        {
            $this->_pluginList[] = $pPlugin;
            return $this;
        }
	
        /**
         * Execute the program.
         * @param   FS_Bootstrap    $bootstrap  If provide, execute Bootstrap.
         */
	public function LaunchProgram(FS_Bootstrap $pBootstrap = NULL)
	{
            if (!is_null($pBootstrap)) {
                $pBootstrap->Run();
            }
            $this->_executePluginMethod(FS_Plugin::INITIALIZE);
            /**
             * TODO: Pas de resolution de route si rewrite n'est pas true. Check route for mariage_galerie, zip route.
             */
            if (!empty(self::$_config[APPLICATION_ENV]['route']['rewrite'])) {
                FS_Router::GetInstance()->SetRoute($_SERVER['REQUEST_URI']);
            }


            $lRequest = FS_Request::GetInstance();
            $lModule = $lRequest->GetModule();
            $lControllerClass = ucfirst($lRequest->GetController());
            $lControllerClass .= 'Controller';
            $lAction = ucfirst($lRequest->GetAction());
            $lAction .= 'Action';

            //Load PreHook file like Identification, LangSelector, ACL, ...

            $this->_executePluginMethod(FS_Plugin::BEFORE_START);

            $lFile = APPLICATION_PATH . self::MODULES . '/' . $lModule . '/' . self::CONTROLLERS . '/' . $lControllerClass . '.php';
            $lLoad = TRUE;
            $lSoftMode = !empty(self::$_config[APPLICATION_ENV]['application']['softMode']);
            if ($lSoftMode) {
                if (!file_exists($lFile)) {
                    $lLoad = FALSE;
                }
            }
            if ($lLoad) {
                require_once($lFile);
                $lFullClassName = strtoupper($lModule) . '_' . $lControllerClass;
                $lController = new $lFullClassName();
                if (!($lController instanceof FS_Controller)) {
                    if ($lSoftMode) {
                        $lLoad = FALSE;
                    } else {
                        FS_Exception::Launch($lControllerClass . ' is not child of FS_Controller.');
                    }
                }
                FS_Layout::GetInstance()->SetController($lController);
                if ($lLoad) {
                    if ($lSoftMode && method_exists($lController, $lAction) === FALSE) {//switch with is_callable
                        $lLoad = FALSE;
                    }
                    if ($lLoad) {
                        $lController->$lAction();
                    }
                }
            }
            //Load Plugin action for beforeRender
            //Load default view
            $this->_executePluginMethod(FS_Plugin::BEFORE_RENDER);
            if (!$lLoad) {
                //404
                $lRender = FS_Layout::GetInstance()->Render();
            } else {
                $lRender = $lController->Render();
            }

            $this->_executePluginMethod(FS_Plugin::BEFORE_OUTPUT, $lRender);

            echo $lRender;
	}
	
	private function loadModules()
	{
		$lList = explode(';', get_include_path());
		$lAppsPath = NULL;
		foreach ($lList AS $lPath) {
			if (file_exists($lPath . 'Fantasite.php')) {
				$lAppsPath = $lPath;
				break;
			}
		}
		if (is_null($lAppsPath)) {
			if (file_exists('Fantasite.php'))
				$lAppsPath = '.';
		}
		
		$lCurrentPath = realpath('.');
		chdir($lAppsPath);
		$this->recurLoadModules();
		chdir($lCurrentPath);
	}
	
	private function recurLoadModules($pName = null)
	{
		$fd = opendir('.');
		while ($file = readdir($fd)) {
			if ($file === '.' || $file === '..') continue;
			if (is_dir($file)) {
				chdir($file);
				$this->recurLoadModules($file);
				chdir('..');
			}
		}
		closedir($fd);
		if (!is_null($pName) && file_exists($pName . '.php'))
			include_once($pName . '.php');
	}
        
        /**
         * Execute plugin method
         * @param string $method    Method to call 
         * @param mixed $args       Argument to pass to methods.
         * @return Fantasite
         */
        private function _executePluginMethod($pMethod, $pArgs = array())
        {
            if (!is_array($pArgs)) {
                $pArgs = array($pArgs);
            }
            
            foreach ($this->_pluginList AS $lPlugin) {
                call_user_func_array(array($lPlugin, $pMethod), $pArgs);
            }
            return $this;
        }
};


















