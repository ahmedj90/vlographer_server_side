<?php

class EventController extends \BaseController {

	public function genre_visited()
	{
		try
		{		
			$validator=$this->validateGenreInformation();      	

	      	if ($validator->passes())
	      	{
	      		$gid=Input::get('gid');
				$uid=Input::get('uid');
				 Event_genre_visited::add_event($uid, $gid);
				
				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>array()
		                    );
			
		        return Response::json($response_array, 200);		
  	
	      	}	
	      	else
	      	{
	      		$response_array = array('success' => false,
	                    'messages' => array("Opps! We can't get this to work right now. We will get it right next time. (Validation issue)"),
	                    'message_type' => 3,
	                    'data' => array());
	            return Response::json($response_array, 400);
	      	}
			

		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Opps! Something wrong happened. We're working on it."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }

	}


	public function channels_seen()
	{
		try
		{		
	        if (Input::has('seen_chid_array') && Helper::isJson(Input::get('seen_chid_array')) )
            {    
	      		$chidArr=json_decode(Input::get('seen_chid_array'));
	      		$uid=Input::get('uid', 0);
	      		
		        if($chidArr==false || count($chidArr)<=0 )
		        {
		        	$response_array = array('success' => false,
	                    'messages' => array("Hmm.. No channel ids were sent."),
	                    'message_type' => 3,
	                    'data' => array());
	            	return Response::json($response_array, 400);
		        }
		        $co=0;

				//log all channels as seen 						 
				foreach($chidArr as $chid)
				{		 
						$row=Event_channel_seen::where('UID', $uid)->where('CHID', $chid)->first();
 
						//insert only non exisintg (CHID + UID) so you dont' have duplicate rows		 
					   // DB::unprepared("insert INTO event_channel_seen set UID='".$uid."', datetime='".Helper::current_datetime()."', CHID=".$chid);			
						if($row==null)
						{
							$co++;
							$suggestion = new Event_channel_seen();
							$suggestion->UID=$uid;
							$suggestion->datetime=Helper::current_datetime();
							$suggestion->CHID=$chid;
							$suggestion->save();				 
						}				
				}

				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>array('co'=>$co)
		                    );
			
		        return Response::json($response_array, 200);		
  	
	      	}	
	      	else
	      	{
	      		$response_array = array('success' => false,
	                    'messages' => array("Opps! We can't get this to work right now. We will get it right next time. (Validation issue)"),
	                    'message_type' => 3,
	                    'data' => array());
	            return Response::json($response_array, 400);
	      	}
			

		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Opps! Something wrong happened. We're working on it."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }
	}

	public function channel_subscribe()
	{
		try
		{		
			$validator=$this->validateGetChannelInformation();      	

	      	if ($validator->passes())
	      	{
	      		$chid=Input::get('chid');
				$uid=Input::get('uid');
				//1-add event 'user subscribed to channel x'
				$event=new Event_channel_subscribe();
				$event->UID= $uid;
				$event->CHID=$chid;
				$event->datetime=Helper::current_datetime();
				$event->save();

				//2-get the subscribed channel friends and add them as suggestions to this user.			
				$friendsList=Channel::getFriends($chid);				
				if($friendsList !=null && count($friendsList)>0)
				{
					foreach($friendsList as $friend)
					{
						$channel=User_suggestions::where('UID',$uid)->where('CHID', $friend->CHID)->first();

						if($channel==null) //if channel is not already suggested
						{
							$suggestion = new User_suggestions();
							$suggestion->UID=$uid;
							$suggestion->parent_CHID=$chid;
							$suggestion->datetime=Helper::current_datetime();
							$suggestion->reason=1;
							$suggestion->CHID=$friend->CHID;
							$suggestion->save();
						}
						
					}
					
				}
				
				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>array()
		                    );
			
		        return Response::json($response_array, 200);		
  	
	      	}	
	      	else
	      	{
	      		$response_array = array('success' => false,
	                    'messages' => array("Opps! We can't get this to work right now. We will get it right next time. (Validation issue)"),
	                    'message_type' => 3,
	                    'data' => array());
	            return Response::json($response_array, 400);
	      	}
			

		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Opps! Something wrong happened. We're working on it."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }

	}

	public function channel_like()
	{
		try
		{		
			$validator=$this->validateGetChannelInformation();      	

	      	if ($validator->passes())
	      	{
	      		$chid=Input::get('chid');
				$uid=Input::get('uid');
				//1-add event 'user subscribed to channel x'
				$event=new Event_channel_like();
				$event->UID= $uid;
				$event->CHID=$chid;
				$event->datetime=Helper::current_datetime();
				$event->save();

				//2-get the subscribed channel friends and add them as suggestions to this user.			
				$friendsList=Channel::getFriends($chid);				
				if($friendsList !=null && count($friendsList)>0)
				{
					foreach($friendsList as $friend)
					{
						$channel=User_suggestions::where('UID',$uid)->where('CHID', $friend->CHID)->first();

						if($channel==null) //if channel is not already suggested
						{
							$suggestion = new User_suggestions();
							$suggestion->UID=$uid;
							$suggestion->parent_CHID=$chid;
							$suggestion->datetime=Helper::current_datetime();
							$suggestion->reason=2;
							$suggestion->CHID=$friend->CHID;
							$suggestion->save();
						}
						
					}
					
				}
				
				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>array()
		                    );
			
		        return Response::json($response_array, 200);		
  	
	      	}	
	      	else
	      	{
	      		$response_array = array('success' => false,
	                    'messages' => array("Opps! We can't get this to work right now. We will get it right next time. (Validation issue)"),
	                    'message_type' => 3,
	                    'data' => array());
	            return Response::json($response_array, 400);
	      	}
			

		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Opps! Something wrong happened. We're working on it."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }

	}

	public function channel_social_media_visited()
	{
		try
		{		
			$validator=$this->validateGetChannelInformation();      	

	      	if ($validator->passes())
	      	{
	      		$chid=Input::get('chid');
				$uid=Input::get('uid');
				$note=Input::get('media');
				if($note =="")
				{					 
		            $response_array = array('success' => false,
		                'messages' => array('No media was sent.'),
		                'message_type' => 1,
		                'data' => array());
		            return Response::json($response_array, 400);
				}
				//1-add event 'user subscribed to channel x'
				$event=new Event_channel_social_visit();
				$event->UID= $uid;
				$event->CHID=$chid;
				$event->datetime=Helper::current_datetime();
				$event->media=Input::get('media');
				$event->save();

				
				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>array()
		                    );
			
		        return Response::json($response_array, 200);		
  	
	      	}	
	      	else
	      	{
	      		$response_array = array('success' => false,
	                    'messages' => array("Opps! We can't get this to work right now. We will get it right next time. (Validation issue)"),
	                    'message_type' => 3,
	                    'data' => array());
	            return Response::json($response_array, 400);
	      	}
			

		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Opps! Something wrong happened. We're working on it."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }

	}

	public function validateGetChannelInformation()
	{
		try
		{
			$data = Input::get();
			$rule = array(
			  'chid' => 'required|integer|min:1|exists:channels,CHID' 			  
			);
			$error_messages = array(
			  'chid' => 'Invalid CHID parameter.' 
			);
			return Validator::make($data, $rule, $error_messages);
		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array('Error validating gernes information.'),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }
	}

	public function validateGenreInformation()
	{
		try
		{
			$data = Input::get();
			$rule = array(
			  'gid' => 'required|integer|min:1|exists:genres,GID' 			  
			);
			$error_messages = array(
			  'gid' => 'Invalid GID parameter.' 
			);
			return Validator::make($data, $rule, $error_messages);
		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array('Error validating gernes information.'),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
