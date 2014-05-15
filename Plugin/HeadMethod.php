<?php

/**
 * Plugin to detect HEAD request and return headers without execute controller/action method to avoid to execute all logic code for nothing.
 */
class FS_Plugin_HeadMethod extends FS_Plugin
{
    const WILDCARD = '*';
    
    private $_moduleControllerAction;
    
    /**
     * Constructor
     * @param array $moduleControllerActionList   List of module/controller/action:
     *                                                  - moduleName:
     *                                                      - controllerName:
     *                                                          - actionName
     * controllerName and actionName value can be * to execute all module or controller.
     */
    public function __construct(array $pModuleControllerActionList = array())
    {
        $this->_moduleControllerAction = $pModuleControllerActionList;
    }
    
    public function BeforeStart()
    {
        $lRequest = FS_Request::GetInstance();
        if (!$lRequest->IsHead()) {
            return TRUE;
        }
        
        $lModule = $lRequest->GetModule();
        if (!isset($this->_moduleControllerAction[$lModule])) {
            return FALSE;
        }
        
        $lModule = $this->_moduleControllerAction[$lModule];
        if ($lModule === self::WILDCARD) {
            return TRUE;
        }
        
        $lController = $lRequest->GetController();
        if (!isset($lModule[$lController])) {
            return FALSE;
        }
        
        $lController = $lModule[$lController];
        if ($lController === self::WILDCARD) {
            return TRUE;
        }
        
        $lAction = $lRequest->GetAction();
        return in_array($lAction, $lController);
    }
}

?>
