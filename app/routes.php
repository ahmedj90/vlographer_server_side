<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('test', function()
{
	return 'test is working!';
});

Route::post('genre', array('as' => 'genres', 'uses' => 'GenreController@getGenres'));

Route::post('genres/channels', array('as' => 'genres_channels', 'before' => 'authentiated', 'uses' => 'GenreController@getGenresChannels'));

Route::post('channels/suggestions', array('as' => 'get_suggested_channels', 'before' => 'authentiated', 'uses' => 'UserSuggestionController@getSuggestedChannels'));

Route::post('channels/liked', array('as' => 'channels_liked', 'before' => 'authentiated', 'uses' => 'ChannelController@getLikedToChannels'));

Route::post('channel/subscribedto', array('as' => 'channels_subscribed_to', 'before' => 'authentiated', 'uses' => 'ChannelController@getSubscribedToChannels'));

Route::post('channel/get', array('as' => 'profile', 'before' => 'authentiated', 'uses' => 'ChannelController@getChannelInformation'));

Route::post('channels/topic', array('as' => 'get_channel_by_topic', 'uses' => 'ChannelController@getChannelsByTopic'));

Route::get('channels/filter/names', array('as' => 'filter_channel_names', 'uses' => 'ChannelController@filterChannelsNames'));


/**** not implemented yet***/
Route::get('channels/filter/topics', array('as' => 'filter_topics', 'uses' => 'ChannelController@filterTopics'));


Route::post('channel/videos', array('as' => 'channel_videos', 'uses' => 'VideoController@getVideos'));

Route::post('events/channel_subscribe', array('as' => 'event_channel_subscribe','before' => 'authentiated', 'uses' => 'EventController@channel_subscribe'));

Route::post('events/channel_like', array('as' => 'event_channel_like','before' => 'authentiated', 'uses' => 'EventController@channel_like'));

 Route::post('events/channels_seen', array('as' => 'event_channel_seen','before' => 'authentiated', 'uses' => 'EventController@channels_seen'));

Route::post('events/genre_visited', array('as' => 'genre_visited','before' => 'authentiated', 'uses' => 'EventController@genre_visited'));

Route::post('events/channels_social_visit', array('as' => 'event_channel_social_medai_visited','before' => 'authentiated', 'uses' => 'EventController@channel_social_media_visited'));

Route::post('user/add', array('as' => 'add_user', 'uses' => 'UserController@add_user'));


Route::filter('authentiated', function()
{      

    if(! (Input::has('uid')  && strlen(Input::get('uid'))<200 &&  User::whereRaw('UID=?', array(Input::get('uid')))->first() !=null) )    
    {
        $response_array = array('success' => false,
            'messages' => array('User id is not valid.'),
            'message_type' => 2,
            'data' => array());
        return Response::json($response_array, 401);   //unauthorized
    }


});

