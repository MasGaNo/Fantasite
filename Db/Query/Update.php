<?php

include_once('Query.php');

/**
 *	Class of Update Query
 */
class	FS_Db_Update extends FS_Db_Query
{
	private $_set;

	protected function _method()
	{
            return 'UPDATE `' . trim($this->_options['table']) . '`';
	}
	
	/**
	 *	Add Set clause
	 *	@param	string|array	$field	Field to update or array of Field => Value
	 *	@param	mixed	$value	Value to set.
	 *	@return	FS_Db_Update
	 */
	public function Set($pField, $pValue = NULL)
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
                FS_Exception::Launch('Argument 1 passed to FS_Db_Update::Set() must be of the type array or string, ' . gettype($pOptions) . ' given');
            }
            return $this;
	}
        
	/**
	 *	Simple add Set clause
	 *	@param	string	$field	Field to update or array of Field => Value
	 *	@param	mixed	$value	Value to set.
	 *	@return	FS_Db_Update
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
		
		if (!is_null($this->_set)) {
			$lQuery .= ' SET ' . implode(', ', $this->_set);
		}
		
		if (!is_null($this->_where)) {
			$lQuery .= ' WHERE ' . implode(' AND ', $this->_where);
		}
		
		if (!is_null($this->_order)) {
			$lQuery .= ' ORDER BY ' . implode(', ', $this->_order);
		}
		
		if (!is_null($this->_limit)) {
			$lQuery .= ' ' . $this->_limit;
		}
		return $this->_query = $lQuery;
	}
}