<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }
    require_once "inc/config.php";

    if ( isset($_POST['login_submit']) ) {
        
        //check if rememberme checkbox is checked and assign value to a variable
        $remember_me = ( isset($_POST['login_chkbox']) ) ? $_POST['login_chkbox'] : "";

        $user_info = array (
            array("Username" => $_POST['login_user_name'],"min"=>3,"max"=>15),
            array("Password" => $_POST['login_password'],"min"=>8,"max"=>20)
            );

        //CODE ABOVE ALREADY IN REGISTER-HANDLER.PHP 

        //validate POST data
        $isValid = $Account->validateAll($con, $user_info, 'login');

        if ( $isValid ) {
            $login_success = $Account->login($_POST['login_user_name'],$_POST['login_password'] );

            
        }
        // else {
        //     error in form data
        // }

    }

?>