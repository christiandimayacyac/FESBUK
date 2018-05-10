<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    include_once "../config.php";

    $wall_posts = "";

    if ( isset($_POST['start']) && isset($_POST['limit']) && isset($_SESSION['user_id']) ) {
        $limit = $_POST['limit'];
        $start = $_POST['start'];
        $user_id = $_SESSION['user_id'];

        // $Post_Obj = new Post($user_id, $limit, $start);
        $Post_Obj = new Post($con, $user_id);
        $wall_posts = $Post_Obj->loadPosts($user_id, $limit, $start);
    }

    echo $wall_posts;

?>