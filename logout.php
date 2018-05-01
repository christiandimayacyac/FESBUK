<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    require_once "inc/config.php";

    if ( isset($_SESSION['user_id']) ){
        unset($_SESSION['user_id']);
    }
    
    if ( isset($_SESSION['user_name']) ){
        unset($_SESSION['user_name']);
    }

    if ( isset($_COOKIE['rememberFesbuk']) ){
		unset($_COOKIE['rememberFesbuk']);
		setcookie('rememberFesbuk',null,-1,'/');
	}


    session_destroy();
    session_write_close();   
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);
    redirectTo('register');
?>