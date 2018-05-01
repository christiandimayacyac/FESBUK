<?php
	class Constant{

		//Form Data Requirements
		
        // public static $username = array("id"=>"Username","min"=>3,"max"=>15);
        // public static $fname = array("id"=>"First name","min"=>2,"max"=>30);
        // public static $lname = array("id"=>"Last name","min"=>2,"max"=>30);
        // public static $email = array("id"=>"Email","min"=>6,"max"=>50);
        // public static $pwd1 = array("id"=>"Password 1","min"=>8,"max"=>20);
		// public static $pwd2 = array("id"=>"Password 2","min"=>8,"max"=>20);

	

		//General Messages
		public static $generic_ajax_error = "Invalid AJAX request encountered";
		public static $generic_db_error = "An error encountered in the database";
		public static $generic_missing_parameter_error = "Missing parameter(s)";
		public static $db_read_error = "Unable to read data from the database";
		public static $db_insert_error = "Unable to register your account";
		public static $db_update_error = "Unable to update record in the database";

		//Accounts
		public static $emailEncKey = "3m@1lk3y";
		public static $userNameEncKey = "u$3rn@m3k3y";
		public static $userIdEncKey = "u$3r1dk3y";
	
		//User-defined Minimum and Maxmimum 
		// public static $un_min_max_id = array("min"=>4, "max"=>25, "id"=>"Username");
		// public static $un_min_max = array("min"=>4, "max"=>25, "id"=>"");
		// public static $fn_min_max_id  = array("min"=>2, "max"=>50, "id"=>"First name");
		// public static $ln_min_max_id  = array("min"=>2, "max"=>50, "id"=>"Last name");
		public static $em_min_max_id  = array("min"=>6, "max"=>50, "id"=>"Email");
		public static $pw_min_max_id  = array("min"=>8,"max"=> 255, "id"=>"Password");

		//Form Errors
		public static $email_exists_err = "Email is already in use";
		public static $user_not_exists = "User does not exists";
		public static $password_err = "Incorrect password";
		public static $password_mismatch_err = "Passwords does not match";
		public static $insert_user_err = "Unable to register your account in the database";

		
		
		//Misc

		public static $separator = "s3p@r@t0r";
	}
?>