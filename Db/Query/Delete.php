<?php

include_once('Query.php');

/**
 *	Class of Delete Query
 */
class	FS_Db_Delete extends FS_Db_Query
{
	protected function _method()
	{
		return 'DELETE';
	}
}