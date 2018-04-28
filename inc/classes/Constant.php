<?php
	class Constant{

		//General Messages
		public static $generic_ajax_error = "Invalid AJAX request encountered.";
		public static $generic_db_error = "An error encountered in the database.";
		public static $generic_missing_parameter_error = "Missing parameter(s).";
		public static $db_read_error = "Unable to read data from the database";
		public static $db_insert_error = "Unable to register your account.";
		public static $db_update_error = "Unable to update record in the database";

		//Accounts
		public static $emailEncKey = "3m@1lk3y";
		public static $userIdEncKey = "u$3r1dk3y";
	
		//User-defined Minimum and Maxmimum 
		// public static $un_min_max_id = array("min"=>4, "max"=>25, "id"=>"Username");
		// public static $un_min_max = array("min"=>4, "max"=>25, "id"=>"");
		// public static $fn_min_max_id  = array("min"=>2, "max"=>50, "id"=>"First name");
		// public static $ln_min_max_id  = array("min"=>2, "max"=>50, "id"=>"Last name");
		public static $em_min_max_id  = array("min"=>6, "max"=>50, "id"=>"Email");
		public static $pw_min_max_id  = array("min"=>8,"max"=> 255, "id"=>"Password");

		//Form Errors
		public static $email_exists_err = "Your email is already registered.";
		public static $user_not_exists = "User does not exists.";
		public static $password_err = "Incorrect Password";

		
		
		//Misc

		public static $separator = "s3p@r@t0r";
	}
?>