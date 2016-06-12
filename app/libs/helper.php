<?php

/*
  Hashing Notes:-
  md2           32 a9046c73e00331af68917d3804f70655
  md4           32 866437cb7a794bce2b727acc0362ee27
  md5           32 5d41402abc4b2a76b9719d911017c592
  sha1          40 aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d
  sha256        64 2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e730
  sha384        96 59e1748777448c69de6b800d7a33bbfb9ff1b463e44354c3553
  sha512       128 9b71d224bd62f3785d96d46ad3ea3d73319bfbc2890caadae2d
 */

class Helper
{

    public static function isJson($string) 
    {
        try
        {
            if(strpos($string,'[') !== false)
            {
                json_decode($string);              
                return (json_last_error() == JSON_ERROR_NONE);
            }
            else
                return false;
        }
        catch (Exception $ex)
        {
            helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            return false;
        } 
     
    }

    static function create_event_file($title, $start_date, $end_date, $desc, $location, $timezone='PST', $status='CONFIRMED')
    {

 try
        {

  $filename = "calendar_files/invite_".helper::generate_unique_filename() .".ics";
  
$data[0]  = "BEGIN:VCALENDAR";
$data[1] = "PRODID:-//Google Inc//Google Calendar 70.9054//EN";
$data[2] = "VERSION:2.0";
$data[3] = "CALSCALE:GREGORIAN";
$data[4] = "METHOD:REQUEST";
$data[8] = "BEGIN:VEVENT";
$data[9] = "DTSTART:".date("Ymd\THis",strtotime($start_date));
$data[10] = "DTEND:".date("Ymd\THis",strtotime($end_date));
$data[11] = "DTSTAMP:".date("Ymd\THis",strtotime($start_date));
$data[12] = "UID:notification@yoagpanda.co";
$data[13] = "CREATED:".date("Ymd\THis",strtotime($start_date));
$data[14] = "DESCRIPTION:".$desc;
$data[15] = "LAST-MODIFIED:".date("Ymd\THis",strtotime($start_date));
$data[16] = "LOCATION:".$location;
$data[17] = "SEQUENCE:0";
$data[18] = "STATUS:".$status;
$data[19] = "SUMMARY:".$title;
$data[20] = "TRANSP:OPAQUE";
$data[21] = "END:VEVENT";
$data[22] = "END:VCALENDAR";

$data = implode("\r\n", $data);
header("text/calendar");
file_put_contents($filename, "\xEF\xBB\xBF".  $data);
return $filename;
}
catch (Exception $ex)
        {
           helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            return '';
        }

    }

    static function send_email($to, $subject, $template_name='empty', $template_parameters_arr=array(), $attach_file_name='')
    {
        try
        {
          if ($attach_file_name!='')
          {
              return Mail::send($template_name, $template_parameters_arr, function($message) use($to,$subject,$attach_file_name) {
                      $message->to($to)->subject($subject);
                      $message->attach($attach_file_name ,['as' => 'Add to calendar']);
              });
          }
          else
          {
              return Mail::send($template_name, $template_parameters_arr, function($message) use($to,$subject ) {
                      $message->to($to)->subject($subject);                       
              });
          }
                
        }
        catch (Exception $ex)
        {
           helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            return 0;
        }
    }


    static function send_bulk_email($bcc, $subject, $template_name='empty', $template_parameters_arr=array(), $attach_file_name='')
    {
        try
        {
          if ($attach_file_name!='')
          {
              return Mail::send($template_name, $template_parameters_arr, function($message) use($to,$subject,$attach_file_name) {
                      $message->bcc($bcc)->subject($subject);
                      $message->attach($attach_file_name ,['as' => 'Add to calendar']);
              });
          }
          else
          {
              return Mail::send($template_name, $template_parameters_arr, function($message) use($to,$subject ) {
                      $message->bcc($bcc)->subject($subject);                       
              });
          }
                
        }
        catch (Exception $ex)
        {
           helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            return 0;
        }
    }

    static function  find_distance($lat1, $lon1, $lat2, $lon2, $unit="K")
    {
/*
echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
 */
 
      
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        
        if ($unit == "K")
        {
            return ($miles * 1.609344);
        }
        else if ($unit == "N")
        {
            return ($miles * 0.8684);
        }
        else
        {
            return $miles;
        }
    }

    public static function get_unique_16_ID()
    {
        return date("ymdHis") . strtoupper(str_random(4));
    }

    public static function get_dates_difference_in_days($_date1, $_date2)
    {
        $date1 = new DateTime($_date1);
        $date2 = new DateTime($_date2);
        $diff = $date2->diff($date1);
        return $diff->days;
    }

    /*
     * Return a random integer
     */

    public static function random_number()
    {
        return rand(0, 99999);
    }

    /*
     * check if date is valid
     */

    public static function is_valid_date($date)
    {
        $data = array('date' => $date);
        $rule = array(
            'date' => 'date'
        );

        $validator = Validator::make($data, $rule);
        if ($validator->passes())
            return true;
        else
            return false;
    }

    /*
     * return current datetime (used in DB).
     */

    public static function current_datetime()
    {
        return date("Y-m-d H:i:s");
    }

    /*
     * Replace http with the used proctol (https or keep it http) and generate link to route
     * @return http link to a route
     */

    public static function generate_http_link_to_route($route_name, $params = array())
    {
        if (empty($params))
            return str_replace("http", application_settings::protocol_user, route($route_name));
        else             //parameters needed
            return str_replace("http", application_settings::protocol_user, route($route_name, $params));
    }

    /*
     * return random 128 code
     */

    public static function generate_random_unique_128_code($salt = 0)
    {
        try
        {
            return hash('sha512', uniqid(rand(), true) . "4Jd38dj" . $salt . "swu8492" . date("s m d H i Y", mktime(0, 0, 0)));
        }
        catch (Exception $ex)
        {
            helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            return null;
        }
    }

    /*
     * Generate session ID
     * @return 128 chars unique sessions.
     */

    public static function generate_unique_sessionID($UID)
    {
        try
        {
            return hash('sha512', "3jj8ejlj3" . $UID . "4syoiuow" . uniqid(rand(), true) . "49e35gsn" . date("s m H d i Y", mktime(0, 0, 0)) . "bnmdjrj2");
            //you can add something here to make sure no session with same ID exist (should be rare)
        }
        catch (Exception $ex)
        {
            helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            return null;
        }
    }

    /*
     * Generate session ID
     * @return 32 chars unique sessions.
     */

    public static function generate_unique_filename()
    {
        try
        {
            return hash('md5', "joi28xsInSWrOewHdxIOMz" . uniqid(rand(), true) . "djeII28JCfrd" . date("m s i d H Y", mktime(0, 0, 0)) . "3jh5HXnrK");
        }
        catch (Exception $ex)
        {
            helper::log_try_error($ex, "Try of " . __CLASS__ . "@" . __FUNCTION__);
            return null;
        }
    }

    /*
     * Log try error
     * 
     */

    public static function log_try_error($error, $custom_msg = "", $very_urgent = false, $err_catgory = 0)
    {
        
        $log = new My_log();
        $log->log_try_error($error, $custom_msg, $very_urgent, $err_catgory);
    }

    /*
     * Log General error
     */

    public static function log_error($custom_msg = "", $urgent = false, $err_catgory = 0)
    {
        $log = new My_log();
        $log->log_error($custom_msg, $urgent, $err_catgory);
    }

}
