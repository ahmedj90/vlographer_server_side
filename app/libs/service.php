<?php

class Service
{
    //this method is just to get info of an already received token which is wrong (unsecure)
    public static function request_fb_info($fb_access_token)
    {
        
        try
        {
            $url = 'https://graph.facebook.com/me?access_token=' . $fb_access_token;
            $opts = array(
                'http' => array('ignore_errors' => true)
            );
            //Create the stream context
            $context = stream_context_create($opts);
            //Open the file using the defined context
            $file = file_get_contents($url, false, $context);
        }
        catch (Exception $ex)
        {
            helper::log_try_error($ex, "Try of ".__CLASS__."@".__FUNCTION__); 
            return FALSE;
        }
        return $file;
    }


   //After we go the client fb code (when the user hit "okay"), let's get the user token on the server now.
    public static function get_fb_client_token($client_fb_code, $client_fb_id, $redirect_url_after_login, $fb_app_secret_code)
    {   
        try
        {
            $url = 'https://graph.facebook.com/v2.3/oauth/access_token?code='.$client_fb_code.'&client_id='.$client_fb_id
                                .'&redirect_uri='.$redirect_url_after_login.'&client_secret='.$fb_app_secret_code;

            $opts = array(
                'http' => array('ignore_errors' => true)
            );

            //Create the stream context
            $context = stream_context_create($opts);
            //Open the file using the defined context
            $file = file_get_contents($url, false, $context);
        }
        catch (Exception $ex)
        {
            helper::log_try_error($ex, "Try of ".__CLASS__."@".__FUNCTION__); 
            return FALSE;
        }
        return $file;
    }


    //this method receives client_code and trys to get user token 
    public static function request_fb_token($fb_client_code)
    {        
        try
        {
            if($fb_client_code=="")
                return FALSE;
            
            $url="https://graph.facebook.com/v2.3/oauth/access_token?
    client_id=982544591765267
   &amp;redirect_uri={redirect-uri}
   &amp;client_secret=6578279977c463e3756399ce4aa3eed4
   &amp;code=".$fb_client_code;

            $url = 'https://graph.facebook.com/me?access_token=' . $fb_access_token;
            $opts = array(
                'http' => array('ignore_errors' => true)
            );
            //Create the stream context
            $context = stream_context_create($opts);
            //Open the file using the defined context
            $file = file_get_contents($url, false, $context);
        }
        catch (Exception $ex)
        {
            helper::log_try_error($ex, "Try of ".__CLASS__."@".__FUNCTION__); 
            return FALSE;
        }
        return $file;
    }
}
