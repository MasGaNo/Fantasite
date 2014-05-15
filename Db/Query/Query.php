<?php

abstract class	FS_Db_Query
{
	const FETCH_ARRAY = 'fetch_array';
	const FETCH_ASSOC = 'fetch_assoc';
	const FETCH_FIELD = 'fetch_fields';
	const FETCH_LENGTHS = 'fetch_lengths';
	const FETCH_OBJECT = 'fetch_object';
	const FETCH_ROW = 'fetch_row';
	
        const PLACE_HOLDER = '?';
        
	private $_db;
	protected $_options;
	
	private $_from;
        private $_leftJoin;
	protected $_where;
	protected $_order;
	protected $_limit;
	
	private $_result;
	
	protected $_query;
	
	/**
	 *	Constructor
	 *	@param	RessourceDb             $db         Connexion to database
	 *	@param	array|string|FS_Db_Expr	$options    Optional options for query
	 */
	public function __construct($pDb, $pOptions = null)
	{
            $this->_db = $pDb;
            if (is_string($pOptions)) {
                $this->_options = array($pOptions);
            } else if (is_array($pOptions) || $pOptions instanceof FS_Db_Expr) {
                $this->_options = $pOptions;
            } else if (!is_null($pOptions)) {
                FS_Exception::Launch('Argument 2 passed to FS_Db_Query::__construct() must be of the type array, string or FS_Db_Expr, ' . gettype($pOptions) . ' given');
            }
	}

	protected abstract function _method();

	/**
	 *	Add from table
	 *	@param	string|array	$from	string for unique table. array for multiple table with possibility to use keys as alias.
	 *	@return FS_Db_Query
	 */
	public function From($pFrom)
	{
            if (is_string($pFrom)) {
                $this->_from[] = '`' . trim($pFrom, '`') . '`';
            } else if (is_array($pFrom)) {
                foreach ($pFrom AS $pTable => $lAlias) {
                    if (is_numeric($pTable)) {
                        $lFrom = '`' . trim($lAlias, '`') . '`';
                    } else {
                        $lFrom = '`' . trim($pTable, '`') . '`';
                        if (is_string($lAlias)) {
                            $lFrom .= ' AS `' . $lAlias . '`';
                        }
                    }
                    $this->_from[] = $lFrom;
                }
            }
            return $this;
	}
	
        /**
         * Add left join
         * @param string|array $join  Table name to join
         * @param array|string $where  List of conditions to join
         * @return \FS_Db_Query
         */
	public function LeftJoin($pJoin, $pWhere)
	{
            if (is_string($pJoin)) {
                $lJoin = '`' . trim($pJoin, '`') . '` ON ';
                if (is_array($pWhere)) {
                    $lJoin .= implode(' AND ', $pWhere);
                } else {
                    $lJoin .= $pWhere;
                }
                $this->_leftJoin[] = $lJoin;
            } else if (is_array($pJoin)) {
                $i = 0;
                foreach ($pJoin AS $lTable => $lAlias) {
                    if (is_numeric($lTable)) {
                        $lJoin = '`' . trim($lAlias, '`') . '` ON ';
                    } else {
                        $lJoin = '`' . trim($lTable, '`') . '` AS `' . $lAlias . '` ON ';
                    }
                    if (is_array($pWhere[$i])) {
                        $lJoin .= implode(' AND ', $pWhere[$i]);
                    } else if (is_array($pWhere)) {
                        $lJoin .= $pWhere[$i];
                    } else {
                        $lJoin .= $pWhere;
                    }
                    $this->_leftJoin[] = $lJoin;
                    ++$i;
                }
            }
            return $this;
	}
	
	public function InnerLeftJoin()
	{
		return $this;
	}
	
	public function RightJoin()
	{
		return $this;
	}
	
	public function InnerRightJoin()
	{
		return $this;
	}
	
    /**
     *	Add Where clause
     *	@param	string	$where	Where clause
     *  @param  mixed   $placeHolder OPTIONAL The value to quote into the condition
     *	@return	FS_Db_Query
     */
    public function Where($pWhere, $pPlaceHolder = NULL)
    {
        if (is_null($pPlaceHolder)) {
            $this->_where[] = $pWhere;
        } else {
            $lCount = preg_match_all('#\?#', $pWhere);
            if (is_array($pPlaceHolder) && $lCount > 1) {
                $lWhere = $pWhere;
                foreach ($pPlaceHolder AS $lPlaceHolder) {
                    $lPos = strpos($lWhere, self::PLACE_HOLDER);
                    if ($lPos === FALSE) {
                        FS_Exception::Launch('Number of values passed in FS_Db_Query::Where doesn\'t match with the number of place holder (' . self::PLACE_HOLDER . ') in the query: ' . $pWhere);
                    }
                    $lWhere = substr_replace($lWhere, $this->Quote($lPlaceHolder), $lPos, 1);
                }
                $this->_where[] = $lWhere;
            } else {
                $this->_where[] = str_replace(self::PLACE_HOLDER, $this->Quote($pPlaceHolder), $pWhere);
            }
        }
        return $this;
    }
	
	public function OrWhere()
	{
		return $this;
	}
	
	public function GroupBy()
	{
		return $this;
	}
	
	/**
	 *	Add Order clause
	 *	@param	string|array	$order      string for unique order clause ASC. array for multiple order clauses. If a key/value is used, the key will be the order clause and the value the ASC/DESC 
	 *	@param	string  	$direction  If $order is string, $direction is used for order clause. Default: ASC
	 *	@return FS_Db_Query
	 */
	public function Order($pOrder, $pDirection = 'ASC')
	{
		if (is_string($pOrder)) {
			$this->_order[] = '`' . trim($pOrder, '`') . '` ' . $pDirection;
		} else if (is_array($pOrder)) {
			foreach ($pOrder AS $lKey => $lValue) {
				if (is_numeric($lKey)) {
					$this->_order[] = '`' . trim($lValue, '`') . '`';
				} else if (is_string($lKey)) {
					$this->_order[] = '`' . trim($lKey, '`') . '` ' . $lValue;
				}
			}
		}
		return $this;
	}
	
	/**
	 *	Add Limit clause
	 *	@param	int	$limit	Number of row on the query.
	 *	@param	int	$start	Start number of query selection.
	 *	@return	FS_Db_Query
	 */
	public function Limit($pLimit, $pStart = null)
	{
		$this->_limit = 'LIMIT ' . $pLimit;
		if (!is_null($pStart)) {
			$this->_limit .= ', ' . $pStart;
		}
		return $this;
	}
	
        /**
         * Execute Query
         * @return \FS_Db_Query
         */
	public function Execute()
	{
            if (is_null($this->_query)) {
                $this->Prepare($this->Assemble());
            }
            $this->_result = mysql_query($this->_query, $this->_db) or FS_Exception::Launch(mysql_error() . "\nQuery: " . $this->_query);
            return $this;
	}
	
	/**
	 *	Get next query's row.
	 *	@param	string	$fetch	Fetch mode or class name to instantiate object. Default: FETCH_OBJECT	
	 *	@param	string	$class	Class name to instantiate object	
	 *	@return	mixed
	 */
	public function FetchRow($pFetch = FS_Db_Query::FETCH_OBJECT, $pClass = NULL)
	{
            if (is_null($pClass) && class_exists($pFetch)) {
                $pClass = $pFetch;
                $pFetch = FS_Db_Query::FETCH_ASSOC;
            }
            
            if (is_null($this->_result)) {
                if (is_null($this->_query)) {
                    $this->Prepare($this->Assemble());
                }
                $this->Execute();
            }
            $method = 'mysql_' . $pFetch;
            if (is_null($pClass)) {
                return $method($this->_result);
            }
            return new $pClass($method($this->_result));
	}
	
	/**
	 *	Get all query's rows.
	 *	@param	string	$fetch	Fetch mode or class name to instantiate all object. Default: FETCH_OBJECT	
	 *	@param	string	$class	Class name to instantiate all object	
	 *	@return	array
	 */
	public function FetchAll($pFetch = FS_Db_Query::FETCH_OBJECT, $pClass = NULL)
	{
            if (is_null($pClass) && class_exists($pFetch)) {
                $pClass = $pFetch;
                $pFetch = FS_Db_Query::FETCH_ASSOC;
            }
            
            if (is_null($this->_result)) {
                    if (is_null($this->_query)) {
                            $this->Prepare($this->Assemble());
                    }
                    $this->Execute();
            }
            $method = 'mysql_' . $pFetch;
            
            if (is_null($pClass)) {
                while ($lDatas[] = $method($this->_result));
                array_pop($lDatas);
            } else {
                while ($lData = $method($this->_result)) {
                    $lDatas[] = new $pClass($lData);
                }
            }
            return $lDatas;
	}
        
        /**
         * Get all query's rows ordered by field name.
         * An array will be returned with key = field's value, and value the data object.
         * Warning: if the field's value is duplicate, the value will be overwritten and the last data object will be kept.
         * @param string $field Field name
	 * @param string $fetch	Fetch mode or class name to instantiate all object. Default: FETCH_OBJECT	
	 * @param string $class	Class name to instantiate all object	
         * @return array
         */
        public function FetchBy($pField, $pFetch = FS_Db_Query::FETCH_OBJECT, $pClass = NULL)
        {
            $datas = $this->FetchAll($pFetch, $pClass);
            $result = array();
            foreach ($datas AS &$data) {
                $result[(object)$data->$pField] = $data;
            }
            return $result;
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
		
		if (!is_null($this->_from)) {
			$lQuery .= ' FROM ' . implode(', ', $this->_from);
		}
                
                if (!is_null($this->_leftJoin)) {
                    $lQuery .= ' LEFT JOIN ' . implode(' LEFT JOIN ', $this->_leftJoin);
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
	
	/**
	 *	Set a full query built manually
	 *	@param	string	$sql	Query
	 *	@return	FS_Db_Query
	 */
	public function Prepare($pSql)
	{
		$this->_query = $pSql;
		return $this;
	}
	
	/**
	 *	Get last id insert
	 *	@return int	Id
	 */
	public function	LastId()
	{
		return mysql_insert_id();
	}
        
    /**
     * Quote value to protect data
     * @param mixed $value  Value to quoted
     * @return mixed
     */
    public function Quote($pValue)
    {
        return self::QuoteValue($pValue);
    }
    
    /**
     * Quote value to protect data
     * @param mixed $value  Value to quoted
     * @return mixed
     */
    static public function QuoteValue($pValue)
    {
        if (is_array($pValue)) {
            foreach ($pValue AS &$lValue) {
                $lValue = self::QuoteValue($lValue);
            }  
            return implode(', ', $pValue);
        } else if ($pValue instanceof FS_Db_Query) {
            $pValue = $pValue->Assemble();
        } else if ($pValue instanceof FS_Db_Expr) {
            //$pValue = $pValue->toString();
            return addcslashes($pValue, "\000\n\r\\'\"\032");
        } else if (!is_string($pValue)) {
            return $pValue;
        }
        return '"' . addcslashes($pValue, "\000\n\r\\'\"\032") . '"';
    }
}