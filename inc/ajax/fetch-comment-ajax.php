<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    include_once "../config.php";

    $comment_html = "";

    if ( isset($_POST['the_comment']) && isset($_POST['post_id']) && isset($_SESSION['user_id']) && isset($_POST['operation']) ) {
        $comment_author = getTrimmedDecodedValue(Constant::$userIdEncKey,$_SESSION['user_id']);
        $comment_body = $_POST['the_comment'];
        $post_id = $_POST['post_id'];

        if ( $_POST['operation'] = "insert" ) {
            $Comment_Obj = new Comment($con, true);

            //get crafted Comment HTML content
            $comment_html = $Comment_Obj->insertComment($comment_author, $comment_body, $post_id);
        }
        
        // if ( $comment_html != "" ) {

        //     echo $comment_html;
        // }
    }
    else if ( isset($_POST['post_id']) && isset($_POST['start']) && isset($_POST['limit']) && isset($_POST['operation']) ) {
        if ( $_POST['operation'] = "fetch" ) {
            //decrypt post_id
            $post_id = $_POST['post_id'];
            // $post_id = getBase64DecodedValue(Constant::$postEncKey, $post_id);
            
            $Comment_Obj = new Comment($con, false);

            //get crafted Comment HTML content
            $comment_html = $Comment_Obj->loadComments($post_id, $_POST['start'], $_POST['limit']);
        }
    }
    else {
        echo "<p>missing post data</p>";
    }




    // else {
    //     //get crafted Comment HTML content
    //     $comment_html = $Comment_Obj->loadComments($comment_author, $comment_body, $post_id);
    // }

    echo $comment_html;

?>