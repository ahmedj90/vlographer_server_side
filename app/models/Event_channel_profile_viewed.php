<?php

 
class Event_channel_profile_viewed extends Eloquent {

	protected $table = 'event_channel_profile_viewed';
	protected $primaryKey = "ID";
	    public $timestamps = false; 
	//protected $hidden = array('view_name', 'active');

	public static function add_event($userId, $chid)
	{
		$event= Event_channel_profile_viewed::where('UID', $userId)->first();
		if($event ==null) //first time visiting this gerne
		{
			$event =new Event_channel_profile_viewed();
			$event->UID=$userId;
			$event->CHID=$chid;
			$event->last_visit_datetime=Helper::current_datetime();
			$event->count=1;
			$event->save();
		}
		else  //not first visit to this gerne
		{
			$event->count +=1;
			$event->last_visit_datetime=Helper::current_datetime();
			$event->save();
		}

	}

}
