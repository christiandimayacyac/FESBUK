<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }
    require_once "inc/config.php";

    //create an Accout Object to hold user input and validation purposes
    $Account = new User($con);

    if ( isset($_POST['reg_submit']) ) { 
        //validate all the data submitted

        $user_info = array (
            array("Username" => $_POST['reg_user_name'],"min"=>3,"max"=>15),
            array("First name" => $_POST['reg_first_name'],"min"=>2,"max"=>30),
            array("Last name" => $_POST['reg_last_name'],"min"=>2,"max"=>30),
            array( "Email" => $_POST['reg_email'],"min"=>6,"max"=>50),
            array("Password 1" => $_POST['reg_password1'],"min"=>8,"max"=>20),
            array("Password 2" => $_POST['reg_password2'],"min"=>8,"max"=>20),
            );

        //validate POST data
        $isValid = $Account->validateAll($con, $user_info, 'register');

        
        if ( $isValid ) {
            $result = $Account->addUser(
                                    $user_info[0]['Username'],
                                    $user_info[1]['First name'],
                                    $user_info[2]['Last name'],
                                    $user_info[3]['Email'],
                                    $user_info[4]['Password 1']
            );

            if ( empty($result) ) {
                //create User SESSIONS
                // $_SESSION['user_id'] = "";y

                //redirect to index.php
                redirectTo('index');
            }
            else {
                //display error
                // var_dump($result);
            }
        }
        
    }

    //Function that redisplays the input data in the form after failed form validation
    function getPrevValue($field_name) {
        if ( isset($_POST[$field_name]) && isset($_POST['reg_submit'])) {
            return $_POST[$field_name];
        }
    }


?>