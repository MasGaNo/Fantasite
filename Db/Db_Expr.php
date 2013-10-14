<?php

/**
 * SQL Expression to pass to arguments.
 */
class FS_Db_Expr
{
    private $_expr;
    private $_alias;
    
    /**
     * 
     * @param string $expr  SQL Expression
     */
    public function __construct($pExpr, $pAlias = null)
    {
        $this->_expr = $pExpr;
        $this->_alias = $pAlias;
    }
    
    public function __toString()
    {
        return $this->_expr . (is_null($this->_alias) ? '' : ' AS "' . $this->_alias . '"');
    }
}

?>
