<?php

/***
 *	Fantasite Exception
 */
class	FS_Exception extends Exception
{
	/***
	 *	Throw a Fantasite Exception
	 *	@param	string	message	Exception message
	 */
	static public function Launch($pMsg)
	{
		$lExcept = new FS_Exception($pMsg);
		throw($lExcept);
	}
}