<?php

 
class Search_log extends Eloquent {

	protected $table = 'search_log';
	protected $primaryKey = "ID";
	//protected $hidden = array('view_name', 'active');

	public static function log($searchString, $uid=0)
	{
		try
		{
			//log the search term for statistics
			$log= new Search_log();			
			$log->term=$searchString;
			$log->UID=$uid;
			$log->save();
		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
        }
		
	}
	 
}
