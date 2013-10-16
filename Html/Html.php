<?php

/**
 * Html element
 */
abstract class FS_Html
{
    const ATTR_NAME = 'name';
    const ATTR_CLASS = 'class';
    const ATTR_ID = 'id';
    
    private $_tag;
    private $_isDouble;
    private $_attr;
    private $_class;
    
    private $_content;
    
    /**
     * Construct Html Element
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
     * @return \FS_Html
     */
    public function AddAttribute($pName, $pValue = '')
    {
        if (!is_null($pValue)) {
            $this->_attr[$pName] = $pValue;
        } else {
            $this->_attr[] = $pName;
        }
        return $this;
    }
    
    /**
     * Get attribute
     * @param string $name  Name of attribute
     * @return mixed
     */
    public function GetAttribute($pName)
    {
        if (isset($this->_attr[$pName])) {
            return $this->_attr[$pName];
        } else {
            foreach ($this->_attr AS $lKey => $lValue) {
                if (is_numeric($lKey)) {
                    return $lValue;
                }
            }
        }
        return NULL;
    }
    
    /**
     * Set name of element
     * @param string $name Name of element
     * @return FS_Html
     */
    public function SetName($pName)
    {
        return $this->SetAttribute(self::ATTR_NAME, $pName);
    }
    
    /**
     * Get name of element
     * @return string
     */
    public function GetName()
    {
        return $this->GetAttribute(self::ATTR_NAME);
    }
    
    /**
     * Set id of element
     * @param string $id Id of element
     * @return FS_Html
     */
    public function SetId($pId)
    {
        return $this->SetAttribute(self::ATTR_ID, $pId);
    }
    
    /**
     * Get id of element
     * @return string
     */
    public function GetId()
    {
        return $this->GetAttribute(self::ATTR_ID);
    }
    
    /**
     * Set content of Html element
     * @param mixed $pContent
     * @return \FS_Html
     */
    public function SetContent($pContent)
    {
        $this->_content = $pContent;
        return $this;
    }
    
    /**
     * Add class to element
     * @param string $class Class of element
     * @return FS_Html
     */
    public function AddClass($pClass)
    {
        $this->_class[] = $pClass;
        return $this;
    }
    
    /**
     * Render Html Element
     * @return string
     */
    public function Render()
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
            if (!is_null($this->_content)) {
                $lRender .= $this->_content;
            }
            $lRender .= "</$this->_tag>";
        }
        return $lRender;
    }
    
    /**
     * Render Html Element
     * @return string
     */
    public function __toString()
    {
        return $this->Render();
    }
};
