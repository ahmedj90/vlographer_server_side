<?php

class UserController extends \BaseController {

	public function add_user()
	{
		try
		{
			$user=new User();
			$uid=Helper::generate_random_unique_128_code("ahmed_salt");
			$user->UID=$uid;
			$user->ip= Input::has("ip")?Input::get('ip'):0;
			$user->client= Input::has("client") && strlen(Input::has("client"))<45?Input::get('client'):'';
			$user->datetime=Helper::current_datetime();
			$user->save();	

			$response_array = array('success' => true,
	                    'messages' => array(),
	                    'message_type' => 1,
	                    'data' => array('uid'=>$uid));
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
