<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    require_once "inc/config.php";

    if ( !isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || !isset($_POST['user_email'])) {
        echo "INVALID POST REQUEST";
        //CLEAR SESSIONS AND FORCE LOGOUT
        exit;
    }
    else{
        //Process AJAX request

        if ( isset($_POST['user_email']) ) {
            return checkIfExists('users', 'email', $_POST['user_email']);
        }
    }

?>