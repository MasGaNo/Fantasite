<?php

include_once('Query.php');

/**
 *	Class of Insert Query
 */
class	FS_Db_Insert extends FS_Db_Query
{
    private $_values;
    private $_countFields;

    private $_set;

    protected function _method()
    {
        $lFields = '';
        if (!is_null($this->_options['fields'])) {
            $this->_countFields = count($this->_options['fields']);
            $lFields .= '(';
            $lList = array();
            foreach ($this->_options['fields'] AS $lField) {
                $lList[] = '`' . trim($lField, '`') . '`';
            }
            $lFields .= implode(', ', $lList) . ')';
        } else {
            $this->_countFields = 0;
        }
        return 'INSERT INTO `' . trim($this->_options['table'], '`') . '`' . $lFields;
    }

    /**
     *	Add Values clause
     *	@param	array	$values	List of values
     *	@return	FS_DB_Insert
     */
    public function	Values(array $pValues)
    {
        if ($this->_countFields && $this->_countFields !== count($pValues)) {
            FS_Exception::Launch('Number of values and number of fields to insert don\'t match.');
        }
        /*
        foreach ($pValues AS $lKey => $lValues) {
            if (is_string($lValues)) {
                $pValues[$lKey] = '"' . mysql_real_escape_string($lValues) . '"';
            }
        }*/
        $this->_values[] = '(' . $this->Quote($pValues) . ')';
        return $this;
    }

    /**
     *	Add Set clause on duplicate key
     *	@param	string|array	$field	Field to update or array of Field => Value
     *	@param	mixed	$value	Value to set.
     *	@return	FS_Db_Insert
     */
    public function OnDuplicateUpdate($pField, $pValue = NULL)
    {
        if (is_string($pField)) {
            $this->_subSet($pField, $pValue);
        } else if (is_array($pField)) {
            foreach ($pField AS $lField => $lValue) {
                if (is_numeric($lField)) {
                    FS_Exception::Launch('Array must be an association of Field => Value to set updated field. Numeric field given.');
                }
                $this->_subSet($lField, $lValue);
            }
        } else {
            FS_Exception::Launch('Argument 1 passed to FS_Db_Insert::OnDuplicateSet() must be of the type array or string, ' . gettype($pOptions) . ' given');
        }
        return $this;
    }

    /**
     *	Simple add Set clause on duplicate key
     *	@param	string	$field	Field to update or array of Field => Value
     *	@param	mixed	$value	Value to set.
     *	@return	FS_Db_Insert
     */
    private function _subSet($pField, $pValue)
    {
        if (is_string($pValue)) {
            $pValue = '"' . mysql_real_escape_string($pValue) . '"';
        }
        $this->_set[] = '`' . trim($pField, '`') . '` = ' . $pValue;
        return $this;
    }

    /**
     *	Assemble and return the full query
     *	@return string
     */
    public function Assemble()
    {
        if (!is_null($this->_query)) {
            return $this->_query;
        }

        $lQuery = $this->_method();

        if (!is_null($this->_values)) {
            $lQuery .= ' VALUES' . implode(', ', $this->_values);
        }

        if (!is_null($this->_set)) {
            $lQuery .= ' ON DUPLICATE KEY UPDATE ' . implode(', ', $this->_set);
        }
        return $this->_query = $lQuery;
    }
}