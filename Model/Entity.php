<?php

/**
 * Entity object
 */
abstract class FS_Model_Entity
{
    protected $_attributes = array();
    
    private $_datas = array();
    
    public function __construct($pDatas = array())
    {
        if ($pDatas instanceof FS_Model_Entity) {
            $pDatas = $pDatas->toArray();
        } else if ($pDatas instanceof stdClass) {
            $pDatas = get_object_vars($pDatas);
        }
        
        foreach ($pDatas AS $key => $value) {
            if (is_numeric($key)) {
                continue;
            }
            if (isset($this->_attributes[$key])) {
                $this->$key = $value;
            }
        }
    }
    
    public function __get($pAttribute)
    {
        if (isset($this->_attributes[$pAttribute])) {
            return $this->_datas[$pAttribute];
        }
        FS_Exception::Launch('Error: ' . $pAttribute . ' is not a valid attribute of ' . get_class($this));
    }
    
    public function __set($pAttribute, $pValue)
    {
        if (isset($this->_attributes[$pAttribute])) {
            $this->_datas[$pAttribute] = $pValue;
            return;
        }
        FS_Exception::Launch('Error: ' . $pAttribute . ' is not a valid attribute of ' . get_class($this));
    }
    
    public function __isset($pAttribute)
    {
        return isset($this->_attributes[$pAttribute]);
    }
    
    public function __unset($pAttribute)
    {
        unset($this->_datas[$pAttribute]);
    }
    
    public function toArray()
    {
        return $this->_datas;
    }
}
