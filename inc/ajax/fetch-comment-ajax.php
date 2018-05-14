<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    include_once "../config.php";

    $wall_posts = "";

    if ( isset($_POST['the_comment']) && isset($_POST['post_id']) && isset($_SESSION['user_id']) ) {
        $comment_author = $_SESSION['user_id'];
        $comment_body = $_POST['the_comment'];
        $post_id = $_POST['post_id'];

        //decrypt post_id
        $post_id = getBase64DecodedValue(Constant::$postEncKey, $post_id);

        $Comment_Obj = new Comment($con, $comment_author, $comment_body, $post_id, true);
        // $Comment_Obj = new Comment($con, $user_id);
        // $wall_posts = $Post_Obj->loadPosts($user_id, $limit, $start);

        if ( $Comment_Obj->insertComment($comment_author, $comment_body, $post_id) ) {
            echo "<span class='comment__content'>" . $_POST['the_comment'] . "</span>";
        }
    }
    else {
        echo "<p>missing post data</p>";
    }

    

?>