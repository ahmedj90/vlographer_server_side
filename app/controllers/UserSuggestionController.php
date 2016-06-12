<?php

class UserSuggestionController extends \BaseController {

	public function getSuggestedChannels()
	{
		try
		{
			$validator=$this->validate();      	

	      	if ($validator->passes())
	      	{	      		
				$limit=Input::get('limit', 0);
				$offset=Input::get('offset', 0);				
				$uid=Input::get('uid',0);
				
				$GID='"GID"';
				$tag='"tag"';
				$CCID='"CCID"';
				$cat_icon='"cat_icon"';
				
				//1-get user suggested channels
				$channels = DB::select( DB::raw("select tmp.*, ch.CHID as referee_chid, ch.name as referee_name, ch.url as referee_url, ch.img_link as referee_img_link from channels as ch inner join (SELECT  videos.high_thumb_url,videos.video_id,videos.VID, c.*, if(ec.UID is not null, 1, 0) as seen, tbl.datetime as datetime_suggested, tbl.parent_CHID as because_used_liked_CHID, tbl.reason, concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM  user_suggestions as tbl inner join channels as c on c.CHID=tbl.CHID inner join videos on videos.VID=c.main_video_id left join channel_main_tags as cg on cg.CHID=tbl.CHID left join (select * from event_channel_seen where UID='".$uid."')  as ec on ec.CHID=tbl.CHID where c.visible=1 and tbl.UID='".$uid."' group by tbl.CHID order by seen, tbl.datetime DESC LIMIT ".$limit." OFFSET ".$offset.") as tmp on tmp.because_used_liked_CHID=ch.CHID"));
				
				//if offset>0 and channels==0 that means there are suggestions but we run out and the limit and offset values sent are meant for the suggestions table not the default suggestions so we can't use them.

				//2-add some staff suggested channels based on number of suggested channels we have for the user				
				if(count($channels)==0) //no suggestions found(never liked a channel), suggest all needed x channels
				{				
					//return $limit channels to user
					$channels = DB::select( DB::raw("SELECT  tbl.rank, videos.high_thumb_url,videos.video_id,videos.VID, c.*, if(ec.UID is not null, 1, 0) as seen, 3 as reason, concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM  default_suggestions as tbl inner join channels as c on c.CHID=tbl.CHID inner join videos on videos.VID=c.main_video_id left join channel_main_tags as cg on cg.CHID=tbl.CHID left join (select * from event_channel_seen where UID='".$uid."')  as ec on ec.CHID=tbl.CHID where c.visible=1 group by tbl.CHID order by seen, tbl.rank  LIMIT ".$limit." OFFSET ".$offset.""));
				}
				else if(count($channels)<$limit) //no enough suggestions available, add some suggestions
				{					
					//NOTE: the limit and offset used here is supposed to be for suggested_channels so 6-12 range might not be available in default table  but it's not a big deal for now.
					//add some suggestions here to fill the gap
					$extra_needed_channels_no = $limit - count($channels);					
					$channels2 = DB::select( DB::raw("SELECT  tbl.rank, videos.high_thumb_url,videos.video_id,videos.VID, c.*, if(ec.UID is not null, 1, 0) as seen, 3 as reason, concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM  default_suggestions as tbl inner join channels as c on c.CHID=tbl.CHID inner join videos on videos.VID=c.main_video_id left join channel_main_tags as cg on cg.CHID=tbl.CHID left join (select * from event_channel_seen where UID='".$uid."')  as ec on ec.CHID=tbl.CHID where c.visible=1 group by tbl.CHID order by seen, tbl.rank  LIMIT ".$extra_needed_channels_no." OFFSET 0"));
					 
					if($channels!=NULL)
						$channels = array_merge($channels, $channels2);
					else
						$channels=$channels2;
				}
				else if(count($channels)==$limit) //there are enough suggestions, add one "UNSEEN" suggestion (optional behavior)
				{
					$no_of_channels_to_suggest=1;

					$channels2 = DB::select( DB::raw("SELECT  tbl.rank, videos.high_thumb_url,videos.video_id,videos.VID, c.*, if(ec.UID is not null, 1, 0) as seen, 3 as reason, concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM  default_suggestions as tbl inner join channels as c on c.CHID=tbl.CHID inner join videos on videos.VID=c.main_video_id left join channel_main_tags as cg on cg.CHID=tbl.CHID left join (select * from event_channel_seen where UID='".$uid."')  as ec on ec.CHID=tbl.CHID where c.visible=1 and ec.UID is null group by tbl.CHID order by seen, tbl.rank  LIMIT ".$no_of_channels_to_suggest." OFFSET 0"));
					 
					if($channels!=NULL && $channels2!=NULL && count($channels2)>0)
					{
						$channels = array_merge($channels, $channels2);					
					 
					}					
				}

				$response_array = array('success' => true,
	                    'messages' => array(),
	                    'message_type' => 1,
	                    'data' => 																																																																																												//'{GID:', cg.GID, ', tag:\"',cg.title,'\",CCID:', cg.CCID,'}'))
	                     $channels);		   
	            
	            return Response::json($response_array, 200);

/*			old code
				$response_array = array('success' => true,
	                    'messages' => array(),
	                    'message_type' => 1,
	                    'data' => 																																																																																												//'{GID:', cg.GID, ', tag:\"',cg.title,'\",CCID:', cg.CCID,'}'))
	                     DB::select( DB::raw("select tmp.*, ch.CHID as referee_chid, ch.name as referee_name, ch.url as referee_url, ch.img_link as referee_img_link from channels as ch inner join (SELECT  videos.high_thumb_url,videos.video_id,videos.VID, c.*, if(ec.UID is not null, 1, 0) as seen, tbl.datetime as datetime_suggested, tbl.parent_CHID as because_used_liked_CHID, tbl.reason, concat( '[', GROUP_CONCAT( CONCAT('{".$cat_icon.":', '\"' ,cg.cat_icon, '\"',', ".$GID.":', cg.GID, ', ".$tag.":\"',cg.title,'\",".$CCID.":', cg.CCID,'}')), ']') categories FROM  user_suggestions as tbl inner join channels as c on c.CHID=tbl.CHID inner join videos on videos.VID=c.main_video_id left join channel_main_tags as cg on cg.CHID=tbl.CHID left join (select * from event_channel_seen where UID='".$uid."')  as ec on ec.CHID=tbl.CHID where c.visible=1 and tbl.UID='".$uid."' group by tbl.CHID order by seen, tbl.datetime DESC LIMIT ".$limit." OFFSET ".$offset.") as tmp on tmp.because_used_liked_CHID=ch.CHID")));		   
	            
	            return Response::json($response_array, 200);
*/
/*

if(items.count ==0) //new user
{
	ds= select channels from inital_user_suggestions limit x, y not seen order by rank
	return ds
}
elseif(items.count<limit) //there are few suggestions
{
	ds=select channels from inital_user_suggestions limit (x-items) not seen order by rank
}
else 
{
	ds=select channels from inital_user_suggestions limit 1 not seen order by rank
}

*/
	            /*SELECT 
    tmp.*,
    ch.name AS referee_name,
    ch.url AS referee_url,
    ch.img_link AS referee_img_link
FROM
    channels AS ch
        INNER JOIN
    (SELECT 
        videos.high_thumb_url,
            videos.video_id,
            videos.VID,
            c.*,
            IF(ec.UID IS NOT NULL, 1, 0) AS seen,
            tbl.datetime AS datetime_suggested,
            tbl.parent_CHID AS because_used_liked_CHID,
            tbl.reason,
            CONCAT('[', GROUP_CONCAT(CONCAT('{GID:', cg.GID, ', tag:"', cg.title, '"}')), ']') categories
    FROM
        user_suggestions AS tbl
    INNER JOIN channels AS c ON c.CHID = tbl.CHID
    INNER JOIN videos ON videos.VID = c.main_video_id
    LEFT JOIN channel_main_tags AS cg ON cg.CHID = tbl.CHID
    LEFT JOIN (SELECT 
        *
    FROM
        event_channel_seen
    WHERE
        UID = '".$uid."') AS ec ON ec.CHID = tbl.CHID
    WHERE
        tbl.UID = '89fc6c6dcec293404284c6ef737f03ccf651e5add284b05aa0bdaaf8501e1cd76b0bdb02a3282644160d141c66a261dc7e27860f20ec9e66876f2a8729ab96dd'
    GROUP BY tbl.CHID
    ORDER BY seen , tbl.rank DESC
    LIMIT 10 OFFSET 0) AS tmp ON tmp.because_used_liked_CHID = ch.CHID
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
