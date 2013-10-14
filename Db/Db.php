<?php

require_once('../Singleton/Singleton.php');
require_once('./Query/Select.php');
require_once('./Query/Update.php');
require_once('./Query/Delete.php');
require_once('./Query/Insert.php');
require_once('Db_Expr.php');

/***
 *	Database manager
 */
class	FS_Db extends FS_Singleton
{
	/**
	 * All mode (Write and Read).
	 */
	const ALL = 'all';
	/**
	 * Only Read mode.
	 */
	const READ = 'read';
	/**
	 * Only Write mode.
	 */
	const WRITE = 'write';
	
	private $_dbRead;
	private $_dbWrite;
	
	//private $__friends = array('FS_Db_Query');

	protected function __construct()
	{
		$lConfig = Fantasite::GetConfig();
		
		if (!is_null($lConfig)) {
			$lConfDb = $lConfig[APPLICATION_ENV]['database'];
			if (isset($lConfDb[FS_Db::WRITE])) {
				$lConf = $lConfDb[FS_Db::WRITE];
				if (isset($lConf['database']))
					$this->Connect($lConf['host'], $lConf['login'], $lConf['password'], $lConf['database'], FS_Db::WRITE);
				else
					$this->Connect($lConf['host'], $lConf['login'], $lConf['password'], '', FS_Db::WRITE);
			}
			if (isset($lConfDb[FS_Db::READ])) {
				$lConf = $lConfDb[FS_Db::READ];
				if (isset($lConf['database']))
					$this->Connect($lConf['host'], $lConf['login'], $lConf['password'], $lConf['database'], FS_Db::READ);
				else
					$this->Connect($lConf['host'], $lConf['login'], $lConf['password'], '', FS_Db::READ);
			}
			if (is_null($this->_dbRead) && is_null($this->_dbWrite)) {
				if (isset($lConfDb['host'])) {
					if (isset($lConfDb['database']))
						$this->connect($lConfDb['host'], $lConfDb['login'], $lConfDb['password'], $lConfDb['database']);
					else
						$this->connect($lConfDb['host'], $lConfDb['login'], $lConfDb['password']);
				}
			} else if (is_null($this->_dbRead)) {
				$this->_dbRead = $this->_dbWrite;
			} else if (is_null($this->_dbWrite)) {
				$this->_dbWrite = $this->_dbRead;
			}
		}
	}
	
	function __destruct()
	{
		if ($this->_dbRead === $this->_dbWrite) {
			mysql_close($this->_dbRead);
		} else {
			mysql_close($this->_dbRead);
			mysql_close($this->_dbWrite);
		}
	}
	
        /**
         * 
         * @return FS_Db
         */
        public static function GetInstance()
        {
            return parent::GetInstance();
        }
        
	public function __get($value)
	{
		/*$trace = debug_backtrace();
		if (isset($trace[1]['class']) && in_array($trace[1]['class'], $this->__friends   )) {
			return $this->$value;
		}
		trigger_error('Cannot access private property ' . __CLASS__ . '::$' . $value, E_USER_ERROR);*/
	}
	
	/**
	 *	Connect to database
	 *	@param	string	$host	Hostname of database
	 *	@param	string	$user	Username of database
	 *	@param	string	$pass	Password of database
	 *	@param	string	$database	Database by default
	 *	@param	string	$mode	Type of connexion. Default: FS_Db::ALL
	 *	@return	FS_Db
	 */
	public function	Connect($pHost, $pUser, $pPass, $pDatabase = '', $pMode = FS_Db::ALL)
	{
		$lDb = mysql_connect($pHost, $pUser, $pPass, TRUE) or FS_Exception::Launch(mysql_error());
		
		switch ($pMode)
		{
			case FS_Db::ALL :
				$this->_dbWrite = $this->_dbRead = $lDb;
				break;
			case FS_Db::READ :
				$this->_dbRead = $lDb;
				break;
			case FS_Db::WRITE :
				$this->_dbWrite = $lDb;
				break;
		}
		
		if ($pDatabase)
			$this->SelectDb($pDatabase, $pMode);
		return $this;
	}
	
	/**
	 *	Select a database
	 *	@param	string	$database	Database by default
	 *	@param	string	$mode	Type of connexion. Default: FS_Db::ALL
	 *	@return	FS_Db
	 */
	public function SelectDb($pDatabase, $pMode = FS_Db::ALL)
	{
		switch ($pMode)
		{
			case FS_Db::ALL :
				if ($this->_dbWrite === $this->_dbRead) mysql_select_db($pDatabase, $this->_dbWrite) or FS_Exception::Launch(mysql_error());
				else {
					mysql_select_db($pDatabase, $this->_dbWrite) or FS_Exception::Launch(mysql_error());
					mysql_select_db($pDatabase, $this->_dbRead) or FS_Exception::Launch(mysql_error());
				}
				break;
			case FS_Db::WRITE :
				mysql_select_db($pDatabase, $this->_dbWrite) or FS_Exception::Launch(mysql_error());
				break;
			case FS_Db::READ :
				mysql_select_db($pDatabase, $this->_dbRead) or FS_Exception::Launch(mysql_error());
				break;
		}
		return $this;
	}
	
	/**
	 *	Create a Select query
	 *	@param	array|string|FS_Db_Expr	$fields	List of field to select. Could be all keys will be used as alias
	 *	@return	FS_Db_Select
	 */
	public function Select($pFields)
	{
            return new FS_Db_Select($this->_dbRead, $pFields);
	}
	
	/**
	 *	Create an Insert query
	 *	@param	string	$table	Table name to insert
	 *	@param	array	$fields	List of field in Insert
	 *	@return	FS_Db_Insert
	 */
	public function Insert($pTable, array $pFields = null)
	{
            return new FS_Db_Insert($this->_dbWrite, array('table'=>$pTable, 'fields'=>$pFields));
	}
	
	/**
	 *	Create a Delete query
	 *	@return	FS_Db_Delete
	 */
	public function Delete()
	{
            return new FS_Db_Delete($this->_dbWrite);
	}
	
	/**
	 *	Create a Update query
	 *	@param	string	$table	Table name to update
	 *	@return	FS_Db_Update
	 */
	public function Update($pTable)
	{
            return new FS_Db_Update($this->_dbWrite, array('table'=>$pTable));
	}
}