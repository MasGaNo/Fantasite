<?php

include_once('Query.php');

/**
 *	Class of Select Query
 */
class	FS_Db_Select extends FS_Db_Query
{
    protected function _method()
    {
        if (is_null($this->_options)) {
            return 'SELECT *';
        } else if (is_string($this->_options)) {
            return 'SELECT `' . trim($this->_options, '` ') . '`';
        } else if ($this->_options instanceof FS_Db_Expr) {
            return 'SELECT ' . $this->_options;
        }
        $lFields = array();
        foreach ($this->_options AS $lAlias => $lOptions) {
            if (!is_numeric($lAlias)) {
                if ($lAlias instanceof FS_Db_Expr) {
                    $lField = $lAlias . ' AS "' . $lOptions . '"';
                } else {
                    $lField = '`' . trim($lAlias, '` ') . '` AS "' . $lOptions . '"';
                }
            } else if ($lOptions instanceof FS_Db_Expr) {
                $lField = $lOptions;
            } else {
                $lField = '`' . trim($lOptions, '` ') . '`';
            }

            $lFields[] = $lField;
        }
        return 'SELECT ' . implode(', ', $lFields);
    }
}