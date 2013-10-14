<?php

abstract class	FS_Singleton
{
	protected function __construct()
	{
		
	}
	
        /**
         * 
         * @staticvar array $sInstance
         * @return FS_Singleton
         */
	/*final */public static function GetInstance()
	{
		static $sInstance = array();
		$lClass = get_called_class();
		
		if (!isset($sInstance[$lClass])) {
			$sInstance[$lClass] = new $lClass();
		}
		
		return $sInstance[$lClass];
	}
	
	final private function __clone() {}
}