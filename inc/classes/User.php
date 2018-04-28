<?php
    class User {
        
        private $con;
        private $errors_array;

        public $user_id;
        public $first_name;
        public $last_name;
        public $email;
        public $hashed_password;
        public $reg_time;

        //initialize a property that will hold errors
        

        public function __construct()  {
            //start a new DB connection
            $this->con = DB::getConnection();
            $this->errors_array = array();
        }

        private function clearErrors() {
            if ( !empty($this->errors_array) ) {
                $this->errors_array = array();
            }
        }

        public function getErrors() {
            return $this->errors_array;
        }

        //return TRUE if all form data valid, otherwise return FALSE
        //set an ERROR message if an error occurs
        public function register($user_name, $first_name, $last_name, $email, $pwd1, $pwd2) {
            //TODO: Validate user data!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            


            //check each if empty data

            
            //query user info and save it to class properties
            $sql_query = "SELECT * FROM users WHERE user_id = :user_id";
            $stmt = $this->con->prepare($sql_query);
            $stmt->execute(array(":user_id"=>$_userid));

            if ( $rs = $stmt->fetch(PDO::FETCH__ASSOC) ) {
                $this->user_id = $rs['user_id'];
                $this->first_name = $rs['user_id'];
                $this->last_name = $rs['user_id'];
                $this->email = $rs['user_id'];
                $this->hashed_password = $rs['user_id'];
                $this->reg_time = $rs['user_id'];

                //create SESSION
                //TODO: ENCRYPT THE SESSIONS VALUES!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                $_SESSION['user_id'] = $this->user_id;
                $_SESSION['email'] = $this->email;
            }
            else {
                $this->error = Constant::$user_not_exists;
                return false;
            }

            return true;
        }

        public function login($user_name, $password) {
            //query user info and save it to class properties
            $sql_query = "SELECT * FROM users WHERE user_id = :user_id";
            $stmt = $this->con->prepare($sql_query);
            $stmt->execute(array(":user_id"=>$_userid));

            if ( $rs = $stmt->fetch(PDO::FETCH__ASSOC) ) {
                $this->user_id = $rs['user_id'];
                $this->first_name = $rs['user_id'];
                $this->last_name = $rs['user_id'];
                $this->email = $rs['user_id'];
                $this->hashed_password = $rs['user_id'];
                $this->reg_time = $rs['user_id'];
            }
            else {
                $this->errors_array = array_push($this->errors_array, Constant::$user_not_exists);
            }
            
            return;
        }

        public function validateAll($form_fields) {
            //check for empty fields
            $this->checkEmptyFields($form_fields);
            //verify email validity
            $this->validateEmail($form_fields['Email']);
            
            return ( empty($this->errors_array[0]) ) ? true : false;
        }

        private function checkEmptyFields($form_fields){

            $form_errors = array();

            foreach($form_fields as $form_field => $form_val){
                //remove white spaces in the input data
                $form_val = preg_replace('/\s/', '', $form_val);

                if( empty($form_val) || $form_val == NULL ){
                        $form_errors[] = $form_field . " is a required field";
                    }
                }
            
            array_push($this->errors_array,  $form_errors);

            return;

        }

        function validateEmail($email){
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($this->errors_array, "Invalid email format"); 
            }

            return;
        }
    }

?>