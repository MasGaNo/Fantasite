<?php

require_once('../Singleton/Singleton.php');

/**
 *	Class to manage Request
 */
class	FS_Request extends FS_Singleton
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_HEAD = 'HEAD';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_TRACE = 'TRACE';
    
    private $_controller;
    private $_action;

    protected function __construct()
    {
        $this->_controller = $this->GetGet('controller', 'Index');
        $this->_action = $this->GetGet('action', 'Index');
    }

    /**
     * 
     * @return FS_Request
     */
    public static function GetInstance()
    {
        return parent::GetInstance();
    }

    public function __get($pKey)
    {
            if ($pKey === 'IsPost') {
                    return count($_POST) > 0; 
            }
            parent::__get($pKey);
    }

    /**
     *	Return current controller
     *	@return string
     */
    public function GetController()
    {
        return $this->_controller;
    }

    /**
     * Set new controller
     * @param string $controller    Controller name
     * @return \FS_Request
     */
    public function SetController($pController)
    {
        $this->_controller = $_GET['controller'] = $pController;
        return $this;
    }

    /**
     *	Return current action
     *	@return string
     */
    public function GetAction()
    {
        return $this->_action;
    }

    /**
     * Set new Action
     * @param string $action    Action name
     * @return \FS_Request
     */
    public function SetAction($pAction)
    {
        $this->_action = $_GET['action'] = $pAction;
        return $this;
    }

    /**
     *	Return current module
     *	@return	string
     */
    public function GetModule()
    {
            $lModule = isset($_SERVER['APPLICATION_MODULE']) ? $_SERVER['APPLICATION_MODULE'] : FS_Url::GetInstance()->subDomain;
            if (empty($lModule))
                    $lModule = 'www';
            return $lModule;
    }

    /**
     *	Return parameter. Check first on POST Request, then on Get Request.
     *	@param	string	$key	Name of the parameter
     *	@param	mixed	$defaultValue	Default value if $key doesn't exist
     *	@return	mixed
     */
    public function GetParam($pKey, $pDefaultValue = NULL)
    {
        $value = $this->GetPost($pKey);
        if ($value !== NULL) {
            return $value;
        }
        return $this->GetGet($pKey, $pDefaultValue);
    }

    /**
     *	Return parameter from $_GET
     *	@param	string	$key	Name of the parameter
     *	@param	mixed	$defaultValue	Default value if $key doesn't exist
     *	@return	mixed
     */
    public function GetGet($pKey, $pDefaultValue = NULL)
    {
        if (isset($_GET[$pKey])) {
            return $_GET[$pKey];
        }
        return $pDefaultValue;
    }

    /**
     *	Return parameter from $_POST
     *	@param	string	$key	Name of the parameter
     *	@param	mixed	$defaultValue	Default value if $key doesn't exist
     *	@return	mixed
     */
    public function GetPost($pKey, $pDefaultValue = NULL)
    {
            if (isset($_POST[$pKey])) {
                    return $_POST[$pKey];
            }
            return $pDefaultValue;
    }

    /**
     * Get method of request
     * @return string
     */
    public function GetMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return $_SERVER['REQUEST_METHOD'];
        }
        return NULL;
    }

    /**
     * Check if request's method is GET
     * @return Boolean
     */
    public function IsGet()
    {
        return $this->GetMethod() === self::METHOD_GET;
    }

    /**
     * Check if request's method is POST
     * @return Boolean
     */
    public function IsPost()
    {
        return $this->GetMethod() === self::METHOD_POST;
    }

    /**
     * Check if request's method is PUT
     * @return Boolean
     */
    public function IsPut()
    {
        return $this->GetMethod() === self::METHOD_PUT;
    }

    /**
     * Check if request's method is PATCH
     * @return Boolean
     */
    public function IsPatch()
    {
        return $this->GetMethod() === self::METHOD_PATCH;
    }

    /**
     * Check if request's method is HEAD
     * @return Boolean
     */
    public function IsHead()
    {
        return $this->GetMethod() === self::METHOD_HEAD;
    }
    
    /**
     * Check if request's method is DELETE
     * @return Boolean
     */
    public function IsDelete()
    {
        return $this->GetMethod() === self::METHOD_DELETE;
    }

    /**
     * Check if request's mode is AJAX
     * @return Boolean
     */
    public function IsAjax()
    {
        return FALSE;
    }
};