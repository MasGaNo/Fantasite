<?php

/**
 *	Abstract class of controller
 */
abstract class	FS_Controller
{
	private $_layout = FALSE;
	private $_script = FALSE;
	
        static private $_includedPaths = array();
        private $_helperList = array();

	/**
	 *	Current view
	 *	@var FS_View
	 */
	protected $view = NULL;
	
	public function __construct()
	{
		$this->view = new FS_View();


		//Checker header request. If normal => SetScript (module/view/script/controllername/actionname.phtml)-> SetLayout(module/view/layout/default)
		//If Ajax SetScript(false)->SetLayout(false). json_encode($this->view->GetVars())
		//If Partial SetScript(module/view/script/controllername/actionname.phtml)->SetLayout(false)->view->render();
                //If Ajax with Fantasite Template, auto get all param with getObjectVars on view and replace by {$name} for generate template based on phtml
		$lAction = FS_Request::GetInstance()->GetAction();
		$this->SetScript($lAction . '.phtml');
		
		$this->_init();
	}
	
	protected abstract function _init();
	
	/**
	 *	Set script to render
	 *	@param	string	$script	Path of the script
	 *	@param	string	$module	If not NULL, set a script from another module
	 *	@return	FS_Controller
	 */
	public function SetScript($pScript, $pModule = NULL)
	{
            if ($pScript === FALSE) {
                $this->_script = FALSE;
                return $this;
            }
		$lPath = /*APPLICATION_PATH . Fantasite::MODULES . '/' . ((is_null($pModule)) ? FS_Request::GetInstance()->GetModule() : $pModule) . '/' . Fantasite::VIEWS . '/' .*/ Fantasite::SCRIPTS . '/';

		$this->_script = $lPath . FS_Request::GetInstance()->GetController() . '/' . $pScript;
				
		return $this;
	}
	
	/**
	 *	Render the script
	 *	@return	string
	 */
	private function renderScript()
	{
		return $this->view->SetScript($this->_script)->Render();
	}
	
	/**
	 *	Set layout to render
	 *	@param	string	$layout	Path of the layout
	 *	@param	string	$module	If not NULL, set a layout from another module
	 *	@return	FS_Controller
	 */
	public function SetLayout($pLayout, $pModule = NULL)
	{
		$lPath = /*APPLICATION_PATH . Fantasite::MODULES . '/' . ((is_null($pModule)) ? FS_Request::GetInstance()->GetModule() : $pModule) . '/' . Fantasite::VIEWS . '/' .*/ Fantasite::LAYOUTS . '/';

		/*$this->_layout = $lPath . $pLayout;*/
		FS_Layout::GetInstance()->SetScript($pLayout, $pModule);
		return $this;
	}
	
	/**
	 *	Render the layout
	 *	@return	string
	 */
	private function renderLayout()
	{
		/*$lLayout = new FS_View();
		return $lLayout->SetScript($this->_layout)->Assign('RenderScript', $this->renderScript)->Render();*/
		return FS_Layout::GetInstance()->Render();
	}
	
	/**
	 *	Render the view
	 *	@return string
	 */
	public function Render()
	{
		if (FS_Layout::GetInstance()->HasScript() !== FALSE) {
			return $this->renderLayout();
		} else if ($this->_script !== FALSE) {
			return $this->renderScript();
		}
		//AJAX, ...
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
        
    /**
     * Get current Request instance.
     * @return FS_Request
     */
    protected function _getRequest()
    {
        return FS_Request::GetInstance();
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
}
