<?php
 
abstract class ErrorType
{
    const Transcoding = 1;
    
    // ErrorType::Transcoding;
    
}

class My_log
{    
    
     public static function log_try_error($error, $custom_msg="", $very_urgent=false, $err_catgory=0)
        {          
            try {
                 Log::error( $custom_msg.".  Error Message: ".$error->GetMessage().", Line:".$error->GetLine(). ", File: ".$error->getFile()."\r\nInput:".  implode("|", Input::all())."\r\n\r\n");
                 if($very_urgent)
                 {
                     //send this in an email or store in another urgent log files to fix the issue as soon as possible.
                 }
            } catch (Exception $ex) {
                Log::error( "Error while trying to log errors in the 'log_Try_error' method in basecontroller class");
            }            
        }
        
         /*
         * Log General error
         */
        public static function log_error($custom_msg="", $urgent=false, $err_catgory=0)
        {
            try {
                 Log::error( $custom_msg.", Input:".implode("|", Input::all())."\r\n\r\n");                 
                 if($urgent==true)
                 {
                     //send an email or store the error in special urgent place
                 }
            } catch (Exception $ex) {
                Log::error( "Error while trying to log errors in the 'log_error' method in basecontroller class");
            }            
        }
    
        
        
}