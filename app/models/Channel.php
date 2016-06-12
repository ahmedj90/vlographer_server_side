<?php

 
class Channel extends Eloquent {

	protected $table = 'channels';
	protected $primaryKey = "CHID";
	//protected $hidden = array('view_name', 'active');

	public static function getFriends($chid)
	{
		return DB::select( DB::raw("SELECT c.*, cr.parent_CHID  FROM (select * from  channel_references where parent_CHID=".$chid.") as cr inner join channels as c on c.CHID=cr.child_CHID where c.visible=1"));
		/*
		SELECT c.*, cr.parent_CHID  FROM (select * from vlographer.channel_references where parent_CHID=1) as cr
inner join channels as c
on c.CHID=cr.child_CHID
*/
	}


}
