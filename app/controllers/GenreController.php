<?php

class GenreController extends \BaseController {
	
	public function getGenres()
	{ 
		try
		{
			$response_array = array('success' => true,
	                    'messages' => array(),
	                    'message_type' => 1,
	                    'data' => Genre::where('active',1)->orderby('rank')->get() );
	        return Response::json($response_array, 200);			
		}
		catch (Exception $ex)
        {
            Helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            $response_array = array('success' => false,
                'messages' => array("Oh crap! We couldn't get any genres! We will fix this very shortly."),
                'message_type' => 1,
                'data' => array());
            return Response::json($response_array, 500);
        }		
	}

	public function getGenresChannels()
	{
		try
		{
			$validator=$this->validate();      	

	      	if ($validator->passes())
	      	{
	      		$genre_text_id=Input::get('genreId');
				$limit=Input::get('limit', 0);
				$offset=Input::get('offset', 0);
				$log_user_visit=Input::has('log_user_visit')?true:false;
				$uid=Input::get('uid',0);

				$genere= Genre::where('text_id',$genre_text_id)->first();		

				if($genere==null)
				{
					$response_array = array('success' => false,
		                    'messages' => array("We're not sure what you're looking for... (Validation issue)"),
		                    'message_type' => 3,
		                    'data' => array());
		            return Response::json($response_array, 400);
				}

				$GID='"GID"';
				$tag='"tag"';
				$CCID='"CCID"';
				$cat_icon='"cat_icon"';

				$response_array = array('success' => true,
	                    'messages' => array(),
	                    'message_type' => 1,
	                    'data' => 
	                    DB::select( DB::raw("SELECT full_channels_genres.*, if(ec.UID is not null, 1, 0) as seen FROM  channel_genre_tag as full_channels_genres left join (select * from event_channel_seen where UID='".$uid."')  as ec on ec.CHID=full_channels_genres.CHID where full_channels_genres.visible=1 and full_channels_genres.GID=".$genere->GID." group by full_channels_genres.CHID order by seen LIMIT ".$limit." OFFSET ".$offset)));
		        //log the user visit to this gerne (if this is a "get more" then don't add log everytime)
		        if($log_user_visit==true)
		        	Event_genre_visited::add_event($uid,$gid);
	            
	            return Response::json($response_array, 200);

	            /*SELECT c.*, if(ec.UID is not null, 1, 0) as seen,
					 
					       concat( '[', GROUP_CONCAT( CONCAT('{GID:', cg.GID, ', tag:"',cg.title,'"}')), ']') categories
					        
					 FROM vlographer.channel_genres as tbl
					inner join channels as c on c.CHID=tbl.CHID
					left join channel_main_tags as cg on cg.CHID=tbl.CHID
					left join (select * from event_channel_seen where UID=1)  as ec on ec.CHID=tbl.CHID

					group by tbl.CHID
					order by seen, tbl.ID
					limit 10 offset 0
					*/
	      	}
	      	else
	      	{
	      		$response_array = array('success' => false,
	                    'messages' => array("Opps! We're not sure what you're looking for.. (Validation issue)"),
	                    'message_type' => 3,
	                    'data' => array());
	            return Response::json($response_array, 400);
	      	}

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

	public function validate()
	{
		try
		{
			$data = Input::get();
			$rule = array(
			  'limit' => 'required|integer|min:1|max:50',
			  'offset' =>'required|integer|min:0',
			  'genreId' => 'required|max:500|exists:genres,text_id,active,1' 			   
			);
			$error_messages = array(
			  'limit' => 'Invalid limit parameter.',
			  'offset'=>'Invalid offset parameter',
			  'genreId'=>'Invalid genereId parameter'
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
