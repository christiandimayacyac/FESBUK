<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    include_once "../config.php";
    

    // if __CONFIG__ is not defined, do not load this file
    // if( !defined('__CONFIG__') ) {
    //     exit('Config File is not defined.');
    // }
    

        if ( isset($_POST['user_id']) ) {
            $PostObj = new Post($con, $_POST['user_id']);

            if ( isset($_POST['post_body']) && isset($_POST['operation'])) {
               
                if ( $_POST['operation'] == "insert" ) {
                    $post_entry_html = $PostObj->submitPost($_POST['post_body'], $recipient = "none", $post_type = 1);
        
                    echo $post_entry_html;
                }
            }
            elseif ( isset($_POST['new_content']) && isset($_POST['post_id']) && isset($_POST['operation']) ) {
                if ( $_POST['operation'] == "edit" ) {
                    $post_edit_html = $PostObj->editPost($_POST['post_id'], $_POST['new_content'], $_POST['user_id']);
        
                    echo $post_edit_html;
                }
            }
            elseif ( isset($_POST['post_id']) && isset($_POST['operation']) ) {
                if ( $_POST['operation'] == "delete" ) {
                    $post_delete_status = $PostObj->deletePost($_POST['post_id'], $_POST['user_id']);
        
                    echo $post_delete_status;
                }
            }
        }

    
    else {
        echo "";
    }
?>