<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }

    //Start Session if not yet started
    if ( !isset($_SESSION) ) {
        session_start();
    }


    //Allow Error Reporting
    //Use only during dev
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');

    //Include the DB.php file for DB connection
    include_once "classes/DB.php";
    include_once "classes/Constant.php";
    include_once "utilities.php";
    include_once "classes/User.php";
    include_once "classes/Post.php";
    include_once "classes/Page.php";
    include_once "classes/Like.php";
    include_once "classes/Comment.php";

    //Create New DB Connection
    $con = DB::getConnection();

?>