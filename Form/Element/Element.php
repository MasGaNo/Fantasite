<?php

/**
 * Form element for each component of form
 */
abstract class FS_Form_Element // extends FS_HTML_Element + deporte these methods in FS_HTML_Element
{
    const ATTR_NAME = 'name';
    const ATTR_CLASS = 'class';
    const ATTR_ID = 'id';
    
    private $_tag;
    private $_isDouble;
    private $_attr;
    private $_class;
    
    private $_label;
    
    /**
     * Construct Form Element
     * @param string $tag   Tag Html element
     * @param Boolean $isDouble If TRUE, close tag.
     */
    public function __construct($pTag, $pIsDouble = TRUE)
    {
        $this->_tag = $pTag;
        $this->_isDouble = $pIsDouble;
        
        $this->_attr = array();
        $this->_class = array();
    }
    
    /**
     * Add attribute
     * @param string $name Name of attribute
     * @param string $value Value of attribute
     * @return \FS_Form_Element
     */
    public function SetAttribute($pName, $pValue = '')
    {
        if (!is_null($pValue)) {
            $this->_attr[$pName] = $pValue;
        } else {
            $this->_attr[] = $pName;
        }
        return $this;
    }
    
    /**
     * Set name of element
     * @param string $name Name of element
     * @return FS_Form_Element
     */
    public function SetName($pName)
    {
        return $this->SetAttribute(self::ATTR_NAME, $pName);
    }
    
    /**
     * Set id of element
     * @param string $id Id of element
     * @return FS_Form_Element
     */
    public function SetId($pId)
    {
        return $this->SetAttribute(self::ATTR_ID, $pId);
    }
    
    /**
     * Add class to element
     * @param string $class Class of element
     * @return FS_Form_Element
     */
    public function AddClass($pClass)
    {
        $this->_class[] = $pClass;
        return $this;
    }
    
    /**
     * Render Form Element
     * @return string
     */
    public function Render()
    {
        return $this->RenderLabel() . $this->RenderElement();
    }
    
    /**
     * Render label
     * @return string
     */
    public function RenderLabel()
    {
        $lLabel = '<label';
        if (isset($this->_attr[self::ATTR_ID])) {
            $lLabel .= ' for="' . $this->_attr[self::ATTR_ID] . '"';
        }
        $label = '>';
        return $label;
    }
    
    /**
     * Render form element.
     * @return string
     */
    public function RenderElement()
    {
        $lRender = "<$this->_tag";
        if (count($this->_class)) {
            $lRender .= ' class="'. implode(' ', $this->_class) . '"';
        }
        foreach ($this->_attr AS $lKey => $lValue) {
            if (is_numeric($lKey)) {
                $lRender .= ' ' . $lValue;
            } else {
                $lRender .= " $lKey=\"$lValue\"";
            }
        }
        $lRender .= '>';
        
        if ($this->_isDouble === TRUE) {
            $lRender .= $this->renderChild() . "$this->_tag";
        }
        return $lRender;
    }
    
    abstract protected function renderChild();
};
