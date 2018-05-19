<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    include_once "../config.php";
    

    // if __CONFIG__ is not defined, do not load this file
    // if( !defined('__CONFIG__') ) {
    //     exit('Config File is not defined.');
    // }

    if ( isset($_POST['post_body'])  && isset($_POST['user_id']) ) {
        //create user and post objects
        // $UserObj = new User($con, $_POST['user_id']);
        $PostObj = new Post($con, $_POST['user_id']);

        $post_entry_html = $PostObj->submitPost($_POST['post_body'], $recipient = "none", $post_type = 1);

        echo $post_entry_html;
    }
    else {
        echo "";
    }
?>