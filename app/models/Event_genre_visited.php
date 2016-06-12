<?php

 
class Event_genre_visited extends Eloquent {

	protected $table = 'event_genre_visited';
	protected $primaryKey = "ID";
	    public $timestamps = false; 
	//protected $hidden = array('view_name', 'active');

	public static function add_event($userId, $genreId)
	{
		$event= Event_genre_visited::where('UID', $userId)->where('GID', $genreId)->first();
		if($event ==null) //first time visiting this gerne
		{
			$event =new Event_genre_visited();
			$event->UID=$userId;
			$event->GID=$genreId;
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
