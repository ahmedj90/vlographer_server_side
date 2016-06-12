<?php

class ChannelController extends \BaseController {
 

	public function getSubscribedToChannels()
	{
		try
		{				
				$uid=Input::get('uid',0);

				$response_array = array('success' => true,
	                    'messages' => array(),
	                    'message_type' => 1,
	                    'data' => 
	                    DB::select( DB::raw("SELECT distinct * FROM  event_channel_subscribe as ec inner join channels as c on c.CHID=ec.CHID where ec.UID='".$uid."' group by c.CHID  order by ec.datetime desc limit 50")));		   
	            
	            return Response::json($response_array, 200);

	            /*SELECT * FROM vlographer.event_channel_subscribe as ec inner join channels as c on c.CHID=ec.CHID
where ec.UID=
				*/
	       

		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Oh crap! We couldn't get channels, this should be fixed very shortly."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }
	}

	public function getLikedToChannels()
	{
		try
		{		
		$validator=$this->validateGetLikedChannels();      	

	      	if (!$validator->passes())
	      	{
				$response_array = array('success' => false,
	                    'messages' => array("Opps! We can't get this to work right now. We will get it right next time. (Validation issue)"),
	                    'message_type' => 3,
	                    'data' => array());
	            return Response::json($response_array, 400);
	      	}		

			$uid=Input::get('uid',0);
			$limit=Input::get('limit');
			$offset=Input::get('offset');

			$response_array = array('success' => true,
                    'messages' => array(),
                    'message_type' => 1,
                    'data' => 
                    DB::select( DB::raw("SELECT  distinct * FROM  event_channel_like as ec inner join channels as c on c.CHID=ec.CHID where ec.UID='".$uid."' group by c.CHID order by ec.datetime desc limit ".$limit." offset ".$offset)));		   
            
            return Response::json($response_array, 200);

	            /*SELECT * FROM vlographer.event_channel_subscribe as ec inner join channels as c on c.CHID=ec.CHID
where ec.UID=
				*/
	       

		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Oh crap! We couldn't get channels, this should be fixed very shortly."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }
	}

	/*
	The user types: "call of", we send request to this method to find all topics with name like %call of% and return top 10 matches based on topic usage number (popularity) importance
	*/
	public function getChannelsByTopic()
	{
		try
		{	
			$validator=$this->validateSearchByTopic();      	

	      	if ($validator->passes())
	      	{
				$topicId=Input::get('topicId',0);
				$uid=Input::get('uid');
				$limit=Input::get('limit');
				$offset=Input::get('offset');

				$GID='"GID"';
				$tag='"tag"';
				$CCID='"CCID"';
				$cat_icon='"cat_icon"';

				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>   DB::select( DB::raw("SELECT distinct c.*, videos.high_thumb_url,videos.video_id,videos.VID, if(ec.UID is not null, 1, 0) as seen, concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM (SELECT * FROM channel_topics where channel_topics.TID=".$topicId.") as tbl inner join channels as c on c.CHID=tbl.CHID inner join videos on videos.VID=c.main_video_id  left join channel_main_tags as cg on cg.CHID=tbl.CHID left join (select * from event_channel_seen where UID='".$uid."')  as ec on ec.CHID=tbl.CHID where c.visible=1  group by tbl.CHID order by seen, c.referees_num DESC, c.referees_total_subscribers_num DESC, c.videos_num_in_last_2months desc, c.subscribers_no desc limit ".$limit." offset ".$offset)));
		
		        return Response::json($response_array, 200);

		        /*
					SELECT c.*, if(ec.UID is not null, 1, 0) as seen,
					      concat( '[', GROUP_CONCAT( CONCAT('{GID:', cg.GID, ', tag:"',cg.title,'"}')), ']') categories
					       
					FROM (SELECT * FROM channel_topics where TID=3) as tbl
					inner join channels as c on c.CHID=tbl.CHID
					left join channel_main_tags as cg on cg.CHID=tbl.CHID
					left join (select * from event_channel_seen where UID=1)  as ec on ec.CHID=tbl.CHID

					group by tbl.CHID
					order by seen, c.referees_num DESC, c.referees_total_subscribers_num DESC, c.videos_num_in_last_2months DESC, c.subscribers_no DESC
					limit 10 offset 0
		        */
	 
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

	/*
	The user types: "joe", we send request to this method to find all channels with name like %joe% and return top 10 matches based on channel importance
	*/
	public function filterChannelsNames()
	{
		try
		{		
			$validator=$this->validateFilter();      	

	      	if (!$validator->passes())
	      	{
	      		return Response::json('', 200);

	      	}	

			$searchString=strtolower(Input::get('searchString',''));
			if($searchString!="" && strlen($searchString)<100) 
			{

				$GID='"GID"';
				$tag='"tag"';
				$CCID='"CCID"';
				$cat_icon='"cat_icon"';			

		        return Response::json(DB::select( DB::raw("SELECT videos.high_thumb_url,videos.video_id,videos.VID, c.*, concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM   channels as c inner join videos on videos.VID=c.main_video_id  left join channel_main_tags as cg on cg.CHID=c.CHID where LOWER(name) like '%".$searchString."%' group by c.CHID order by  c.referees_num DESC, c.referees_total_subscribers_num DESC, c.videos_num_in_last_2months DESC, c.subscribers_no DESC limit 10 "))
		        	, 200);

		       /*  SELECT 
				    c.*,
				    CONCAT('[',
				            GROUP_CONCAT(CONCAT('{GID:',
				                        cg.GID,
				                        ', tag:',
				                        cg.title,
				                        ', order:',
				                        cg.category_order,
				                        '}')),
				            ']') categories
				FROM
				    channels AS c
				        LEFT JOIN
				    channel_main_tags AS cg ON cg.CHID = c.CHID
				WHERE
				    c.name LIKE '%joe%'
				GROUP BY c.CHID
				ORDER BY c.referees_num DESC , c.referees_total_subscribers_num DESC , c.videos_num_in_last_2months DESC , c.subscribers_no DESC
				LIMIT 10
				*/
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
	 
	 public function filterTopics()
	{
		try
		{	
			$validator=$this->validateFilter();      	

	      	if (!$validator->passes())
	      	{
	      		return Response::json('', 200);

	      	}		
			$searchString=strtolower(Input::get('searchString',''));
			if($searchString!="" && strlen($searchString)<100) 
			{
				 
				//log the search term for statistics
				Search_log::log($searchString, Input::has('uid')?Input::get('uid',0):0);
				 
		        return Response::json( Topic::whereRaw("LOWER(topic) like '%".trim($searchString)."%'")
						                    ->orderBy('num_used', 'desc')
						                    ->take(10)
						                    ->get()
						                    , 200);
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

	public function getChannelInformation()
	{
		try
		{		
			$validator=$this->validateGetChannelInformation();      	

	      	if ($validator->passes())
	      	{
	      		$chid=Input::get('chid');
				$uid=Input::get('uid');

				$GID='"GID"';
				$tag='"tag"';
				$CCID='"CCID"';
				$cat_icon='"cat_icon"';

				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>array('channel_info'=> DB::select( DB::raw("SELECT videos.high_thumb_url,videos.video_id,videos.VID, c.*,concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM   channels as c inner join videos on videos.VID=c.main_video_id  left join channel_main_tags as cg on cg.CHID=c.CHID where c.CHID=".$chid."  group by c.CHID ")),
		                    			   'channel_videos'=> Video::where('CHID', $chid)->orderBy('published_at', 'desc')->take(10)->get(),
		                    			   'channel_friends'=>Channel::getFriends($chid),
		                    			   'social_media'=>Social_media::where('CHID', $chid)->orderByRaw("case when css_class_name = 'fui-twitter' then 1  when css_class_name = 'fui-instagram' then 2 when css_class_name = 'fui-facebook' then 3 when css_class_name = 'fui-tumblr' then 4  when css_class_name = 'fui-wordpress' then 5  when css_class_name = 'fui-google-plus' then 6  when css_class_name = 'fui-pinterest' then 7 when css_class_name = 'fui-twitch' then 8 else 9 end")->get())
		                    );
				Event_channel_profile_viewed::add_event($uid, $chid);
		        return Response::json($response_array, 200);		
 
	            /*SELECT 
					    c.*,
					    CONCAT('[',
					            GROUP_CONCAT(CONCAT('{GID:',
					                        cg.GID,
					                        ', tag:"',
					                        cg.title,
					                        '"}')),
					            ']') categories
					FROM
					    channels AS c
					        LEFT JOIN
					    channel_main_tags AS cg ON cg.CHID = c.CHID
					WHERE
					    c.CHID = 48654
					GROUP BY c.CHID
					*/		
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

	public function validateSearchByTopic()
	{
		try
		{
			$data = Input::get();
			$rule = array(
			  'topicId' => 'required|integer|min:1|exists:topics,ID',
			  'limit' => 'required|integer|min:1|max:50',
			  'offset' =>'required|integer|min:0'			  
			);
			$error_messages = array(
			  'topicId' => 'Invalid TopicId parameter.',
			  'limit' => 'Invalid limit parameter.',
			  'offset'=>'Invalid offset parameter'
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

public function validateGetLikedChannels()
	{
		try
		{
			$data = Input::get();
			$rule = array(
			  
			  'limit' => 'required|integer|min:1|max:50',
			  'offset' =>'required|integer|min:0'			  
			);
			$error_messages = array(
			   
			  'limit' => 'Invalid limit parameter.',
			  'offset'=>'Invalid offset parameter'
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

public function validateFilter()
	{
		try
		{
			$data = Input::get();
			$rule = array(
			  'searchString' => 'required|min:1|max:50' 
			);
			$error_messages = array(
			  'searchString' => 'Invalid string parameter.' 
			);
			return Validator::make($data, $rule, $error_messages);
		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array('Error validating validateFilter information.'),
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
