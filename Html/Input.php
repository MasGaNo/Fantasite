<?php

/**
 * Input Html element
 */
class FS_Html_Input extends FS_Html
{
    const ATTR_TYPE = 'type';
    const ATTR_VALUE = 'value';
    const ATTR_SIZE = 'size';
    const ATTR_LENGTH = 'length';
    
    /**
     * Construct Input Html Element
     * @param string $type   Type of input
     */
    public function __construct($pType)
    {
        parent::__construct('input', FALSE);
        $this->AddAttribute(self::ATTR_TYPE, $pType);
    }
    
    /**
     * Get type
     * @return string
     */
    public function GetType()
    {
        return $this->GetAttribute(self::ATTR_TYPE);
    }
    
    /**
     * Set type
     * @param string $type
     * @return \FS_Html_Input
     */
    public function SetType($pType)
    {
        $this->AddAttribute(self::ATTR_TYPE, $pType);
        return $this;
    }
    
    /**
     * Get value
     * @return string
     */
    public function GetValue()
    {
        return $this->GetAttribute(self::ATTR_VALUE);
    }
    
    /**
     * Set value
     * @param string $value
     * @return \FS_Html_Input
     */
    public function SetValue($pValue)
    {
        $this->AddAttribute(self::ATTR_VALUE, $pValue);
        return $this;
    }
    
    /**
     * Get size
     * @return int
     */
    public function GetSize()
    {
        return $this->GetAttribute(self::ATTR_SIZE);
    }
    
    /**
     * Set size
     * @param int $size
     * @return \FS_Html_Input
     */
    public function SetSize($pSize)
    {
        $this->AddAttribute(self::ATTR_SIZE, $pSize);
        return $this;
    }
    
    /**
     * Get length
     * @return int
     */
    public function GetLength()
    {
        return $this->GetAttribute(self::ATTR_LENGTH);
    }
    
    /**
     * Set length
     * @param int $length
     * @return \FS_Html_Input
     */
    public function SetLength($pLength)
    {
        $this->AddAttribute(self::ATTR_LENGTH, $pLength);
        return $this;
    }
};
