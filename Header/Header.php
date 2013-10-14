<?php

require_once('../Singleton/Singleton.php');

/**
 *	Class to manage Header
 */
class	FS_Header extends FS_Singleton
{
	const DOCTYPE_HTML5 = 'html5';
	const DOCTYPE_HTML4 = 'html4';
	const DOCTYPE_XHTML1STRICT = 'xhtml1strict';
	const DOCTYPE_XHTML1TRANSITIONAL = 'xhtml1transitional';
	
	const ELEMENTS = 1;
	const META = 2;
	const SCRIPT = 4;
	const CSS = 8;
	
	const FULL_HEAD = 15;

	private $_htmlAttributes;
	private $_headAttributes;
	private $_bodyAttributes;
	
	private $_partMeta;
	private $_partCss;
	private $_partScript;
	private $_partHeadElements;
	
	private $_title;
	private $_doctype;
	
	private $_includeModifiedDate;
	
	protected function __construct()
	{
		$this->_htmlAttributes = array();
		$this->_headAttributes = array();
		$this->_bodyAttributes = array();
		
		$this->_partMeta = array();
		$this->_partCss = array();
		$this->_partScript = array();
		$this->_partHeadElements = array();
		
		$lConfig = Fantasite::GetConfig();
		$this->_title = isset($lConfig['html']['defaultTitle']) ? $lConfig['html']['defaultTitle'] : '';
		
		$this->_doctype = self::DOCTYPE_HTML5;
		
		$lConfig = Fantasite::GetConfig(TRUE);
		$this->_includeModifiedDate = isset($lConfig['html']['script']['includeModifiedDate']) ? $lConfig['html']['script']['includeModifiedDate'] : false;
	}

        /**
         * 
         * @return FS_Header
         */
        public static function GetInstance()
        {
            return parent::GetInstance();
        }
        
	/**
	 *	Set doctype
	 *	@param	string	$doctype
	 *	@return	FS_Header
	 */
	public function SetDoctype($pDoctype)
	{
		$this->_doctype = $pDoctype;
	}
	
	/**
	 *	Render doctype
	 *	@return string
	 */
	public function RenderDoctype()
	{
		$lDoctype = '';
		switch ($this->_doctype)
		{
			case self::DOCTYPE_HTML5:
				$lDoctype = '<!DOCTYPE html>';
				break;
			case self::DOCTYPE_HTML4:
				$lDoctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
				break;
			case self::DOCTYPE_XHTML1STRICT:
				$lDoctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
				break;
			case self::DOCTYPE_XHTML1TRANSITIONAL:
				$lDoctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				break;
		};
		return $lDoctype;
	}
	
	/**
	 *	Set new title page
	 *	@param	string	$title	Title of the page
	 *	@return FS_Header
	 */
	public function SetTitle($pTitle)
	{
		$this->_title = $pTitle;
		return $this;
	}
	
	/**
	 *	Render title
	 *	@return string
	 */
	public function RenderTitle()
	{
		return '<title>' . $this->_title . '</title>';
	}
	
	/**
	 *	Add attribute to HTML Tag
	 *	@param	string|array	$attribute	Name of the attribute or list of attributes.
	 *	@param	string	$value	Value of the attribute
	 *	@return	FS_Header
	 */
	public function AddHtmlAttribute($pAttribute, $pValue = NULL)
	{
		if (is_array($pAttribute)) {
			foreach ($pAttribute AS $lKey => $lValue) {
				if (is_numeric($lKey)) {
					$this->_htmlAttributes[] = $lValue;
				} else {
					$this->_htmlAttributes[] = $lKey . '="' . $lValue . '"';
				}
			}
		} else {
			if (!empty($pValue)) {
				$pAttribute .= '="' . $pValue . '"';
			}
			$this->_htmlAttributes[] = $pAttribute;
		}
		return $this;
	}
	
	/**
	 *	Add attribute to HEAD Tag
	 *	@param	string|array	$attribute	Name of the attribute or list of attributes.
	 *	@param	string	$value	Value of the attribute
	 *	@return	FS_Header
	 */
	public function AddHeadAttribute($pAttribute, $pValue = NULL)
	{
		if (is_array($pAttribute)) {
			foreach ($pAttribute AS $lKey => $lValue) {
				if (is_numeric($lKey)) {
					$this->_headAttributes[] = $lValue;
				} else {
					$this->_headAttributes[] = $lKey . '="' . $lValue . '"';
				}
			}
		} else {
			if (!empty($pValue)) {
				$pAttribute .= '="' . $pValue . '"';
			}
			$this->_headAttributes[] = $pAttribute;
		}
		return $this;
	}
	
	/**
	 *	Add attribute to BODY Tag
	 *	@param	string|array	$attribute	Name of the attribute or list of attributes.
	 *	@param	string	$value	Value of the attribute
	 *	@return	FS_Header
	 */
	public function AddBodyAttribute($pAttribute, $pValue = NULL)
	{
		if (is_array($pAttribute)) {
			foreach ($pAttribute AS $lKey => $lValue) {
				if (is_numeric($lKey)) {
					$this->_bodyAttributes[] = $lValue;
				} else {
					$this->_bodyAttributes[] = $lKey . '="' . $lValue . '"';
				}
			}
		} else {
			if (!empty($pValue)) {
				$pAttribute .= '="' . $pValue . '"';
			}
			$this->_bodyAttributes[] = $pAttribute;
		}
		return $this;
	}
	
	/**
	 *	Add new script
	 *	@param	string	$script	Path of script
	 *	@param	array	$attributes	List of attributes
	 *	@return	FS_Header
	 */	
	public function AddScript($pScript, Array $pAttributes = array())
	{
            //replace by strrpos('.')
		$pAttributes['src'] = ($this->_includeModifiedDate && file_exists($pScript)) ? substr($pScript, 0, -3) . '.' . filemtime($pScript) . '.js' : $pScript;
		$this->_partScript[] = $pAttributes;
		return $this;
	}
	
	/**
	 *	Add new CSS
	 *	@param	string	$css	Path of CSS
	 *	@param	array	$attributes	List of attributes
	 *	@return	FS_Header
	 */	
	public function AddCss($pCss, Array $pAttributes = array())
	{
            //replace by strrpos('.')
		$pAttributes['href'] = ($this->_includeModifiedDate && file_exists($pCss)) ? substr($pCss, 0, -4) . '.' . filemtime($pCss) . '.css' : $pCss;
		$this->_partCss[] = $pAttributes;
		return $this;
	}
	
	/**
	 *	Add new Meta
	 *	@param	array	$attributes	List of attributes
	 *	@return	FS_Header
	 */	
	public function AddMeta(Array $pAttributes = array())
	{
		$this->_partMeta[] = $pAttributes;
		return $this;
	}
	
	/**
	 *	Add new element to head
	 *	@param	string	$element Name of the element
	 *	@param	array	$attributes	List of attributes
	 *	@param	Boolean	$isSingleTag	If true, create a single tag
	 *	@return	FS_Header
	 */
	public function AddHeadElement($pElement, Array $pAttributes = array(), $pIsSingleTag = TRUE)
	{
		$this->_partHeadElements[] = array('element' => $pElement, 'attributes' => $pAttributes, 'isSingleTag' => $pIsSingleTag);
		return $this;
	}
	
	/**
	 *	Render HTML tag without end tag
	 *	@return string
	 */
	public function RenderHtml()
	{
		return $this->_renderElement('html', $this->_htmlAttributes);
	}
	
	/**
	 *	Render Head tag
	 *	@param	bool	$fullRender	If TRUE, render full Head with elements according to $renderMode
	 *	@param	int		$renderMode	If $fullRender is TRUE, list of elements to include to render Head.
	 *	@return string
	 */
	public function RenderHead($pFullRender = TRUE, $pRenderMode = self::FULL_HEAD)
	{
		$lHead = $this->_renderElement('head', $this->_htmlAttributes);
		
		if ($pFullRender === FALSE) return $lHead;
		
		$lHead .= "\n" . $this->RenderTitle() . "\n";
		
		if ($pRenderMode & self::CSS === self::CSS) {
			$lHead .= $this->RenderCss();
		}
		if ($pRenderMode & self::META === self::META) {	
			$lHead .= $this->RenderMeta();
		}
		if ($pRenderMode & self::ELEMENTS === self::ELEMENTS) {
			$lHead .= $this->RenderElements();
		}
		if ($pRenderMode & self::SCRIPT === self::SCRIPT) {
			$lHead .= $this->RenderScript();
		}
		
		$lHead .= '</head>';
		return $lHead;
	}
	
	/**
	 *	Render BODY tag without end tag
	 *	@return string
	 */
	public function RenderBody()
	{
		return $this->_renderElement('body', $this->_bodyAttributes);
	}
	
	/**
	 *	Render CSS Element
	 *	@return	string
	 */
	public function RenderCss()
	{
		$lCss = '';
		$lDefaultAttributes = array('rel' => 'stylesheet', 'media' => 'screen', 'type' => 'text/css');
		foreach ($this->_partCss AS $lElement) {
			$lCss .= $this->_renderElement('link', array_merge($lDefaultAttributes, $lElement));
		}
		return $lCss;
	}
	
	/**
	 *	Render Meta Element
	 *	@return	string
	 */
	public function RenderMeta()
	{
		$lMeta = '';
		foreach ($this->_partMeta AS $lElement) {
			$lMeta .= $this->_renderElement('meta', $lElement);
		}
		return $lMeta;
	}
	
	/**
	 *	Render Head Elements
	 *	@return	string
	 */
	public function RenderHeadElements()
	{
		$lElements = '';
		foreach ($this->_partHeadElements AS $lElement) {
			$lElements .= $this->_renderElement($lElement['element'], $lElement['attributes'], $lElement['isSingleTag']);
		}
		return $lElements;
	}
	
	/**
	 *	Render Script Element
	 *	@return	string
	 */
	public function RenderScript()
	{
		$lScript = '';
		foreach ($this->_partScript AS $lElement) {
			$lScript .= $this->_renderElement('script', $lElement, FALSE);
		}
		return $lScript;
	}
	
	/**
	 *	Render full page.
	 *	@param	FS_View|string	$view	If not NULL, render the view inside body element and close the page
	 *	@param	array	$datas	If $view is not NULL, provide $datas
	 *	@return	string
	 */
	public function Render($pView = NULL, $pDatas = array())
	{
		$lPage = $this->RenderDoctype();
		$lPage .= $this->RenderHead();
		$lPage .= $this->RenderBody();
		
		if (is_null($pView)) return $lPage;
		
		if (is_string($pView)) {
			$lView = new FS_View();
			$lPage .= $lView->SetScript($pView)->Assign($pDatas)->Render();
		} else if ($pView instanceof FS_View){
			$lPage .= $lView->Assign($pDatas)->Render();
		} else {
			FS_Exception('$view must be an instance of FS_View or string.');
		}
		$lPage .= "</body>\n</html>";
		return $lPage;
	}
	
	/**
	 *	Render an element
	 *	@param	string	$name	Name of element
	 *	@param	array	$attributes	List of attributes
	 *	@param	bool	$isSingle	TRUE if is a simple element
	 *	@return	string
	 */
	private function _renderElement($pName, Array $pAttributes = array(), $pIsSingle = TRUE)
	{
		$lElement = '<' . $pName;
		if (count($pAttributes)) {
                        foreach ($pAttributes AS $key => $value) {
                            if (is_numeric($key)) {
                                $lElement .= ' ' . $value;
                            } else {
                                $lElement .= ' ' . $key . '="' . $value . '"';
                            }
                        }
		}
		$lElement .= ">";
		
		if ($pIsSingle === FALSE) {
			$lElement .= '</' . $pName . '>';
		}
		$lElement .= "\n";
		return $lElement;
	}
};

