<?php

include_once('Query.php');

/**
 *	Class of Insert Query
 */
class	FS_Db_Insert extends FS_Db_Query
{
	private $_values;
	private $_countFields;
	
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
		foreach ($pValues AS $lKey => $lValues) {
			if (is_string($lValues)) {
				$pValues[$lKey] = '"' . mysql_real_escape_string($lValues) . '"';
			}
		}
		$this->_values[] = '(' . implode(', ', $pValues) . ')';
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
		return $this->_query = $lQuery;
	}
}