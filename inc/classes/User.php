<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }

    class User {
        
        private $con;
        private $errors_array;

        public $user_id;
        public $user_name;
        public $first_name;
        public $last_name;
        public $email;
        public $hashed_password;
        public $profile_pic = "default.png";
        public $reg_time;
        public $num_posts;
        public $num_likes;
        public $upload_path = "assets/img/uploads/";
        

        public function __construct($con, $user_id=0)  {
            //start a new DB connection
            $this->con = $con;
            $this->errors_array = array();

            if ( $user_id > 0 ) {
                $this->getUserInfo($user_id);
            }
        }

        private function clearErrors() {
            if ( !empty($this->errors_array) ) {
                $this->errors_array = array();
            }
        }

        public function getErrors() {
            return $this->errors_array;
        }


        //Function to insert input data to database after passing validation
        public function addUser($u_name, $f_name, $l_name, $email, $pwd) {
            //declare a variable that will hold DB operations result
            $db_error = array();

            //get hashed value of password
            $hashed_pwd = $this->getPwdHashedValue($pwd);

            // $prof_pic = "../../assets/img/uploads/" . $this->profile_pic;

            try{
               $sql_insert  = "INSERT INTO users (user_name, first_name, last_name, email, password) VALUES (:user_name, :first_name, :last_name, :email, :hashed_password) ";
               $stmt = $this->con->prepare($sql_insert);
               $stmt->execute(array(':user_name'=>$u_name, ':first_name'=>$f_name, ':last_name'=>$l_name, ':email'=>$email, ':hashed_password'=>$hashed_pwd));

               if ( $stmt->rowCount() == 1 ) {
                    $lastInsertedId = getTrimmedEncodedValue(Constant::$userIdEncKey, $this->con->lastInsertId());
                    $this->createUserSession('user_id', $lastInsertedId);
                    $this->createUserSession('user_name', "$u_name");
               }
            }
            catch(PDOException $ex){
               array_push($db_error, Constant::$insert_user_err . $ex->getMessage());
            }

            return $db_error;
        }

        


        public function login($user_name, $password) {

            $login_success = false;
            $this->clearErrors();

            //query user info and save it to class properties
            $sql_query = "SELECT * FROM users WHERE user_name = :user_name";
            $stmt = $this->con->prepare($sql_query);
            $stmt->execute(array(":user_name"=>$user_name));

            if ( $rs = $stmt->fetch(PDO::FETCH_ASSOC) ) {

                //verify password
                if ( password_verify($password, $rs['password']) ) {
                    
                    $this->setClassProperties($rs);
                    //encrypt the user_id
                    // $encryptedTrimmedUserId = getTrimmedEncodedValue(Constant::$userIdEncKey, $rs['user_id']);
                    $encryptedTrimmedUserId = getTrimmedEncodedValue(Constant::$userIdEncKey, $rs['user_id']);
                    //create User Sessions
                    // $this->createUserSession('user_id', $encryptedTrimmedUserId);
                    $this->createUserSession('user_id', $encryptedTrimmedUserId);
                    $this->createUserSession('user_name', $rs['user_name']);

                    $login_success = true;
                }
                else {
                    array_push($this->errors_array, Constant::$password_err);
                }  
            }
            else {
                array_push($this->errors_array, Constant::$user_not_exists);
            }

            return $login_success;

        }

        public function incrementLikes($num_likes) {
            $num_likes++;
            return updateTableField($this->con, "users", "num_likes", $num_likes, "user_id", $this->user_id);
        }

        public function decrementLikes($num_likes) {
            $num_likes--;
            return updateTableField($this->con, "users", "num_likes", $num_likes, "user_id", $this->user_id);
        }

        //Get User Info
        public function getUserInfo($user_id) {

            $this->clearErrors();

            //query user info and save it to class properties
            $sql_query = "SELECT * FROM users WHERE user_id = :user_id";
            $stmt = $this->con->prepare($sql_query);
            $stmt->execute(array(":user_id"=>$user_id));

            if ( $rs = $stmt->fetch(PDO::FETCH_ASSOC) ) {

                $this->setClassProperties($rs);

            }
            else {

                array_push($this->errors_array, Constant::$user_not_exists);

            }
        }

        private function setClassProperties($rs) {

            if ( !empty($rs) ) {
                $this->user_id = $rs['user_id'];
                $this->user_name = $rs['user_name'];
                $this->first_name = $rs['first_name'];
                $this->last_name = $rs['last_name'];
                $this->email = $rs['email'];
                $this->hashed_password = $rs['password'];
                $this->reg_time = $rs['reg_time'];
                $this->profile_pic = $rs['profile_pic'];
                $this->num_posts = $rs['num_posts'];
                $this->num_likes = $rs['num_likes'];
            }

        }

        public function getNumPosts() {
            return $this->num_posts;
        }


        //function that returns the hashed value
        private function getPwdHashedValue($raw_data) {
            return ( !empty($raw_data) ) ? password_hash($raw_data, PASSWORD_DEFAULT) : NULL;
        }

        //Main validation call
        public function validateAll($con, $form_fields, $form_type) {

            if ( $form_type == "register" ) {

                //check for empty fields
                $this->checkEmptyFields($form_fields);
                //check for field lengths
                $this->checkMinLength($form_fields);
                //verify email validity
                $this->validateEmail($form_fields[3]['Email']); //see register-handler for multi-dim array structure reference
                //check if the email is available
                if ( $this->checkIfExists($con, 'users', 'email', $form_fields[3]['Email']) && empty($this->errors_array) ) array_push($this->errors_array, Constant::$email_exists_err);
                //validate 2 passwords
                if ( !$this->validatePassword($form_fields) && empty($this->errors_array) ) array_push($this->errors_array, Constant::$password_mismatch_err);

            }
            else{

                //check for empty fields
                $this->checkEmptyFields($form_fields);
                //check for field lengths
                $this->checkMinLength($form_fields);

            }
            
            //if returns TRUE, proceed with the registration
            return ( empty($this->errors_array) ) ? true : false;
        }

        //checks if fields are empty or NULL
        private function checkEmptyFields($form_fields){

            $form_errors = array();

            foreach($form_fields as $form_field => $form_val){ // $form_val contains all the sub arrays
    

                $the_key = key($form_val); //contains the CURRENT key name on each sub array
                $the_val = current($form_val); //contains the CURRENT value element on each sub array

                //remove white spaces in the input data
                $form_val[$the_key] = preg_replace('/\s+/', '', $form_val[$the_key]);

                if( empty($form_val[$the_key]) || $form_val[$the_key] == NULL ){
                        $form_errors[] = $the_key . " is a required field";
                    }
                }
            
                $this->errors_array = array_merge($this->errors_array,  $form_errors);

            return;

        }

        //checks if the specified record exists in the database
        public function checkIfExists($con, $table_name, $field_name, $search_string) {

            //previous validation must be cleared before proceeding this validation
            if ( !empty($this->errors_array) ) {
                return;
            }
            
            $sql_query = "SELECT $field_name FROM $table_name WHERE $field_name = :search_string LIMIT 1";
            $stmt=$con->prepare($sql_query);
            $stmt->execute(array(":search_string"=>$search_string));

            return ( $stmt->rowCount() == 1 ) ? true : false;
        }

        //checks the string length agains min and max
        //$form_fields contains a multi-dimensional array containg form data and required min and max str length
        private function checkMinLength($form_fields) {

            //previous validation must be cleared before proceeding this validation
            if ( !empty($this->errors_array) ) {
                return;
            }

            $form_errors = array();

            foreach($form_fields as $form_field => $form_val){ // $form_val contains all the sub arrays
    
                $the_key = key($form_val);
                $the_min = $form_val['min']; 
                $the_max = $form_val['max']; 


                if ( $the_key != "Email" ) {
                    if( !(strlen($form_val[$the_key]) >= $the_min)  && strlen($form_val[$the_key]) <= $the_max || empty($form_val[$the_key])){
                        if (  $the_key == "Password 1"  || $the_key == "Password 2" || $the_key == "Password" ) {
                            // array_push($form_errors, "Password must be $the_min to $the_max characters."); 
                            array_push($form_errors, "Password must be ". $the_min . " to " . $the_max . "characters."); 
                        }
                        else{
                            // array_push($form_errors, "$the_key must be $the_min to $the_max characters."); 
                            array_push($form_errors, $the_key . " must be " . $the_min . " to " . $the_max . " characters."); 
                        }
                        
                    }
                    else {

                    }
                }
            }
            
            $this->errors_array = array_merge($this->errors_array,  $form_errors);
            // $this->errors_array = $newArray;

			return;
        }

        //validates email formatting
        private function validateEmail($email){
            
            //previous validations must be cleared before proceeding this validation
            if ( !empty($this->errors_array) ) {
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($this->errors_array, "Invalid email format"); 
            }

            return;
        }

        private function validatePassword($form_fields){
            //previous validation must be cleared before proceeding this validation
            if ( !empty($this->errors_array) ) {
                return;
            }

            $pwd1 = $form_fields[4]['Password 1']; //see register-handler for 
            $pwd2 = $form_fields[5]['Password 2']; //multi-dim array structure reference

            return ( $pwd1 === $pwd2 ) ? true : false;
        }

        private function createUserSession($session_name, $session_value) {
            $_SESSION[$session_name] = $session_value;
            // $_SESSION[$session_name] = '123';

            return;
        }
        
        
    }

?>