<?php

class User extends Eloquent  {

    protected $table = 'users';    // The database table used by the model.
    public $timestamps = false;  //This will update the "creted_at" and "updated_at"         
    //protected $hidden = array('password');  // The attributes excluded from the model's JSON form.	 	      
    protected $primaryKey = "UID";

    
    
    //All functions that has signedin key word in their names are meant to take data from the session (memory signins table), not db.
    
      public static function does_user_exist($uid) {
        // Must not already exist in the `account name` column of `users` table
        $rules = array('uid' => 'exists:users,UID'); 
        $validator = Validator::make(array('uid'=> $uid), $rules);

        if ($validator->fails())
            return true;
        else
            return false;        
    }

    
     
}
