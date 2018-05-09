<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    include_once "../config.php";

    if ( isset($_POST['post_id']) && isset($_POST['like_flag']) && isset($_SESSION['user_id']) && isset($_POST['operation']) ) {
        //declare a variable to hold the overall operation success flag
        $update_done = false;

        //decode the encrypted post_id from the POST data
        $decoded_pid = getBase64DecodedValue(Constant::$postEncKey, $_POST['post_id']);
        $user_id = $_SESSION['user_id'];

        //create like,post and user objects
        $UserObj = new User($con, $user_id);
        $PostObj = new Post($con, $user_id);
        $LikeObj = new Like($con, $decoded_pid, $user_id);

        //set the properties of the POST object based on the decoded post_id
        $PostObj->getPostsDetails($decoded_pid);

        //get the current number of likes in posts and users table
        $post_num_likes = $PostObj->post_likes;
        $user_num_likes = $UserObj->num_likes;

        //update the LIKES, POSTS and USERS table///////////////
        
        if ( $_POST['operation'] == "insert" ) {
            //LIKES table
            $isLikeUpdated = $LikeObj->insertLike();
            //POSTS table
            $isPostLikesUpdated = $PostObj->incrementLikes($post_num_likes);
            //USERS table
            $isUsersLikesUpdated = $UserObj->incrementLikes($user_num_likes);
            
            
        }
        else {
            //LIKES table
            $isLikeUpdated = $LikeObj->deleteLike();
            //POSTS table
            $isPostLikesUpdated = $PostObj->decrementLikes($post_num_likes);
            //USERS table
            $isUsersLikesUpdated = $UserObj->decrementLikes($user_num_likes);
        }

        $update_done = true;
    }

    echo $update_done;
?>