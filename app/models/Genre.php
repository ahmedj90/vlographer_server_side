<?php

 
class Genre extends Eloquent {

	protected $table = 'genres';
	protected $primaryKey = "GID";
	protected $hidden = array('view_name', 'active');

}
