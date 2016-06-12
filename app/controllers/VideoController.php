<?php

class VideoController extends \BaseController {

	public function getVideos()
	{

		try
		{		
			$validator=$this->validateGetChannelInformation();      	

	      	if ($validator->passes())
	      	{
	      		$chid=Input::get('chid');				 
				$limit=Input::get('limit', 0);
				$offset=Input::get('offset', 0);

				$response_array = array('success' => true,
		                    'messages' => array(),
		                    'message_type' => 1,
		                    'data' =>Video::where('CHID',$chid)
										 ->take($limit)
										 ->skip($offset)
										 ->orderBy('published_at', 'desc')
										 ->get()
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
			  'chid' => 'required|integer|min:1|exists:channels,CHID',
			  'limit' => 'required|integer|min:1|max:50',
			  'offset' =>'required|integer|min:0'	  
			);
			$error_messages = array(
			  'chid' => 'Invalid CHID parameter.',
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
