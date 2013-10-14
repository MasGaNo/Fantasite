<?php

require_once('../View/View.php');

/**
 *	Class of layout
 */
class	FS_Layout extends FS_View
{
	private $_controller = NULL;
	private $_layout = FALSE;
	
	private static $_instance = NULL;

	public function __construct()
	{
		$lConf = Fantasite::GetConfig(TRUE);
		if (isset($lConf['layout']['default'])) {
			$lPath = /*APPLICATION_PATH . Fantasite::MODULES . '/' . FS_Request::GetInstance()->GetModule() . '/' . Fantasite::VIEWS . '/' .*/ Fantasite::LAYOUTS . '/';

			$this->_layout = $lPath . $lConf['layout']['default'];
			$this->SetScript($this->_layout);
		}
	}
	
	/**
	 *	Set controller
	 *	@param	FS_Controller	$controller	Controller to render
	 *	@return	FS_Layout
	 */
	public function SetController(FS_Controller $pController)
	{
		$this->_controller = $pController;
		return $this;
	}
	
	/**
	 *	Render Controller script
	 *	@return string
	 */
	public function RenderScript()
	{
		$lContent = '';
		if (!is_null($this->_controller)) {
			$lContent = $this->saveScript()->_controller->Render();
			$this->reloadScript();
		}
		return $lContent;
	}

	/**
	 *	Override. If a layout is not defined, render the Controller Script
	 *	@return string
	 */
	public function Render()
	{
		if ($this->HasScript())
			return parent::Render();
		return $this->RenderScript();
	}
	
	/**
	 *	Return unique instance of FS_Layout
	 *	@return FS_Layout
	 */
	final public static function GetInstance()
	{
		if (is_null(self::$_instance)) self::$_instance = new FS_Layout();

		return self::$_instance;
	}
}