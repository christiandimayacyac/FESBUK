<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    include_once "../config.php";

    $wall_posts = "";

    if ( isset($_POST['the_comment']) && isset($_POST['post_id']) && isset($_SESSION['user_id']) ) {
        $comment_author = getTrimmedDecodedValue(Constant::$userIdEncKey,$_SESSION['user_id']);
        $comment_body = $_POST['the_comment'];
        $post_id = $_POST['post_id'];

        //decrypt post_id
        $post_id = getBase64DecodedValue(Constant::$postEncKey, $post_id);

        $Comment_Obj = new Comment($con, $comment_author, $comment_body, $post_id, true);
        // $Comment_Obj = new Comment($con, $user_id);
        // $wall_posts = $Post_Obj->loadPosts($user_id, $limit, $start);

        //get crafted Comment HTML content
        $comment_html = $Comment_Obj->insertComment($comment_author, $comment_body, $post_id);
        if ( $comment_html != "" ) {

            echo $comment_html;
        }
    }
    else {
        echo "<p>missing post data</p>";
    }

    

?>