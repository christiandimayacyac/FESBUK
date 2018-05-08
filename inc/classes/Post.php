<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }

    class Post {

        public $post_class_stat;

        private $con;

        private $User_Obj;
        public $post_id;
        public $post_author;
        public $post_recipient;
        public $post_body;
        public $post_date_posted;
        public $post_date_edited;
        public $post_deleted;
        public $public_post_type;
        public $post_likes;

        public $cur_user_id;

        private $posts_array = array();

        
        public function __construct($con, int $user_id) {
            $this->con = $con;
            $this->User_Obj = new User($this->con, $user_id);

            // $this->getPostsDetails();
        }

        private function isEpmptyString($raw_str) {
            $raw_str = strip_tags($raw_str);
            $raw_str = preg_replace('/\s+/', '', $raw_str);

            return ( $raw_str === "" ) ? true : false;
        }

        private function getPostTimeInterval($date_time_diff) {
            switch (true) {
                case $date_time_diff->y == 1:
                    $time_label = " a year ago";
                    break;
                case $date_time_diff->y > 1:
                    $time_label = "$date_time_diff->y years ago";
                    break;
                case $date_time_diff->m == 1:
                    $time_label = " a month ago";
                    break;
                case $date_time_diff->m > 1:
                    $time_label = "$date_time_diff->m months ago";
                    break;
                case $date_time_diff->d == 1:
                    $time_label = " a day ago";
                    break;
                case $date_time_diff->d > 1:
                    $time_label = "$date_time_diff->d days ago";
                    break;
                case $date_time_diff->h == 1:
                    $time_label = " an hour ago";
                    break;
                case $date_time_diff->h > 1:
                    $time_label = "$date_time_diff->h hours ago";
                    break;
                case $date_time_diff->i == 1:
                    $time_label = " a minute ago";
                    break;
                case $date_time_diff->i > 1:
                    $time_label = "$date_time_diff->i  minutes ago";
                    break;
                case $date_time_diff->s == 1:
                    $time_label = " a second ago";
                    break;
                case $date_time_diff->s > 1:
                    $time_label = " $date_time_diff->s seconds ago";
                    break;
                default:
                    $time_label = " just now";
                    break;
            }

            return $time_label;
        }

        public function submitPost(string $post_body, $recipient = "none", $post_type = 1) {  
            $date_posted = date("Y-m-d H:i:s");
            $author_id = $this->User_Obj->user_id;

            if ( !$this->isEpmptyString($post_body) ) {
                //if validated and passed save data to table
                $sql_insert = "INSERT INTO posts (post_author, post_recipient, post_body, date_posted, date_edited, public_post_type) 
                                VALUES (:post_author, :post_recipient, :post_body, :date_posted, :date_edited, :public_post_type)";
                $stmt = $this->con->prepare($sql_insert);
                $stmt->execute(array(":post_author"=>$author_id , ":post_recipient"=>$recipient, ":post_body"=>$post_body, ":date_posted"=>$date_posted, ":date_edited"=>$date_posted, ":public_post_type"=>$post_type));

                //get last Post_Id inserted
                $last_post_id = $this->con->lastInsertId();
                //encrypt post_id to be used as data attribute
                $post_id = getBase64EncodedValue(Constant::$postEncKey, $last_post_id);

                //update num_posts in users' table
                $num_posts = $this->User_Obj->getNumPosts();
                $num_posts++;
                // $result = $this->updateNumPosts($num_posts, $author_id);
                updateTableField($this->con, "users", "num_posts", $num_posts, "user_id", $author_id);

                // echo "The result is $result";

                //post to the user's wall////////////////////////////
                $profile_pic = "assets/img/uploads/" . $this->User_Obj->profile_pic;
                $post_author = $this->User_Obj->first_name . " " . $this->User_Obj->last_name;

                //calculate time passed after posting
                $current_date_time = date("Y-m-d H:i:s");
                $start_date_time = new DateTime($date_posted);
                $end_date_time = new DateTime($current_date_time);
                $date_time_diff = $start_date_time->diff($end_date_time);

                $time_label = $this->getPostTimeInterval($date_time_diff);

                

                //if POST is for user's own wall  craft a POST-ENTRY HTML
                if ( $recipient == "none" ) {
                    $post_html = "<div class='post-entry' data-pid='$post_id'>
                                    <div class='post-header'>
                                        <img src='$profile_pic' class='post-header__img'>
                                        <div class='post-header__details'>
                                            <span class='post-header__author'>$post_author</span>
                                            <span class='post-header__date-posted'>$time_label</span>
                                        </div>
                                        <a href='#' class='post-header__edit-btn'>Edit</a>
                                    </div>

                                    <p class='post-body'>$post_body</p>
                                    <div class='post-footer'>
                                        <button class='post-footer__link js--like'>
                                            <svg class='post-footer__icon'>
                                                <use xlink:href='assets/img/sprite.svg#icon-thumbs-up'></use>
                                            </svg>
                                            Like
                                        </button>
                                        <button class='post-footer__link'>
                                            <svg class='post-footer__icon'>
                                                <use xlink:href='assets/img/sprite.svg#icon-message'></use>
                                            </svg>
                                            Comment
                                        </button>
                                    </div>
                                  </div>";

                    return $post_html;
                }

            }
            
            return "";
        }

        public function loadPosts($user_id) {
            
            if ( $user_id > 0 ) {
                $sql_query = "SELECT * FROM posts WHERE post_author = :user_id ORDER BY date_posted DESC LIMIT 5";
                $stmt = $this->con->prepare($sql_query);    
                $stmt->execute(array(":user_id"=>$user_id));

                
                //retrieve user's profile pic
                $profile_pic = "assets/img/uploads/" . $this->User_Obj->profile_pic;
                //retrieve post author's name
                // $post_author = $this->User_Obj->first_name . " " . $this->User_Obj->last_name;
                $post_author = "{$this->User_Obj->first_name} {$this->User_Obj->last_name}";
                
                while ($post_entry = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $post_html = "";
                    //retrieve post id and body
                    $post_id = getBase64EncodedValue(Constant::$postEncKey, $post_entry['post_id']);
                    $post_body = $post_entry['post_body'];

                    //calculate time passed after posting
                    $current_date_time = date("Y-m-d H:i:s");
                    $start_date_time = new DateTime($post_entry['date_posted']);
                    $end_date_time = new DateTime($current_date_time);
                    $date_time_diff = $start_date_time->diff($end_date_time);


                    $time_label = $this->getPostTimeInterval($date_time_diff);

                    //check if current_user liked the current post; add "js--liked" class to like button
                    $Like_Obj = new Like($this->con, $post_entry['post_id'], $user_id);
                    $svg_html = ( $Like_Obj->isLikeEntryExists() ) ? "<svg class='post-footer__icon js--liked'>" : "<svg class='post-footer__icon'>";

                    //if POST is for user's own wall  craft a POST-ENTRY HTML
                    // if ( $recipient == "none" ) {
                        $post_html .= "<div class='post-entry' data-pid='$post_id'>
                                            <div class='post-header'>
                                                <img src='$profile_pic' class='post-header__img'>
                                                <div class='post-header__details'>
                                                    <span class='post-header__author'>$post_author</span>
                                                    <span class='post-header__date-posted'>$time_label</span>
                                                </div>
                                                <a href='#' class='post-header__edit-btn'>Edit</a>
                                            </div>

                                            <p class='post-body'>$post_body</p>
                                            <div class='post-footer'>
                                                <button class='post-footer__link js--like'>
                                                    $svg_html
                                                        <use xlink:href='assets/img/sprite.svg#icon-thumbs-up'></use>
                                                    </svg>
                                                    Like
                                                </button>
                                                <button class='post-footer__link js--comment'>
                                                    <svg class='post-footer__icon'>
                                                        <use xlink:href='assets/img/sprite.svg#icon-message'></use>
                                                    </svg>
                                                    Comments
                                                </button>
                                            </div>
                                        </div>";
                        echo $post_html;
                    // }
                }
                
            }

        }


        public function incrementLikes($num_likes) {
            $num_likes++;
            return updateTableField($this->con, "posts", "likes", $num_likes, "post_id", $this->post_id);
        }

        public function decrementLikes($num_likes) {
            $num_likes--;
            return updateTableField($this->con, "posts", "likes", $num_likes, "post_id", $this->post_id);
        }



        public function getPostsDetails($post_id) {

            if ( $this->User_Obj->getErrors() != Constant::$user_not_exists ) {
                try {
                    // $sql_query = "SELECT * FROM posts WHERE post_author = :user_id ORDER BY post_date_created DESC";
                    $sql_query = "SELECT * FROM posts WHERE post_id = :post_id ORDER BY date_posted DESC";
                    $stmt = $this->con->prepare($sql_query);
                    $stmt->execute(array(":post_id"=>$post_id));

                    if ( $rs = $stmt->fetch(PDO::FETCH_OBJ) ) {

                        $this->post_id = $rs->post_id;
                        $this->post_author = $rs->post_author;
                        $this->post_recipient = $rs->post_recipient;
                        $this->post_body = $rs->post_body;
                        $this->post_date_created = $rs->date_posted;
                        $this->post_date_edited = $rs->date_edited;
                        $this->post_deleted = $rs->post_deleted;
                        $this->public_post_type = $rs->public_post_type;
                        $this->post_likes = $rs->likes;
    
                    }
                    else {
                        //User does not exist
                        $this->post_class_stat = Constant::$user_not_exists;
                    }
                }
                catch(PDOException $ex) {
                    //throw an error
                    // return "db error:" . $ex->getMessage();
                }
                
            }
        }

    }


?>