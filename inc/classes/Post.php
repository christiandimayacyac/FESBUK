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

        
        public function __construct($con, $user_id) {
            $this->con = $con;
            // var_dump("Post Class user_id: " . $user_id);
            $decoded_user_id = getTrimmedDecodedValue(Constant::$userIdEncKey, $user_id);
            $this->User_Obj = new User($this->con, $decoded_user_id);

            // $this->getPostsDetails();
        }

        private function isEpmptyString($raw_str) {
            $raw_str = strip_tags($raw_str);
            $raw_str = preg_replace('/\s+/', '', $raw_str);

            return ( $raw_str === "" ) ? true : false;
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
                //remove the 2 trailing "=" signs in the encrypted $post_id value to be used as a class name
                $trimmed_post_id = substr_replace($post_id,"",-2);

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

                $time_label = getPostTimeInterval($date_time_diff);

                

                //if POST is for user's own wall  craft a POST-ENTRY HTML
                if ( $recipient == "none" ) {
                    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
                    $posts_html = "<div class='post-entry' data-pid='$post_id' data-uid='$user_id'>
                                    <div class='post-content'>
                                        <div class='post-header'>
                                            <img src='$profile_pic' class='post-header__img'>
                                            <div class='post-header__details'>
                                                <span class='post-header__author'>$post_author</span>
                                                <span class='post-header__date-posted'>$time_label</span>
                                            </div>
                                            <button class='post-header__options-btn'>...</buton>
                                        </div>

                                        <p class='post-body js--post-body'>$post_body</p>
                                        <div class='post-footer'>
                                            <button class='post-footer__link js--like'>
                                                <svg class='post-footer__icon'>
                                                    <use xlink:href='assets/img/sprite.svg#icon-thumbs-up'></use>
                                                </svg>
                                                Like
                                            </button>
                                            <button class='post-footer__link js--comment' aria-controls='post-comment-form'>
                                                <svg class='post-footer__icon'>
                                                    <use xlink:href='assets/img/sprite.svg#icon-message'></use>
                                                </svg>
                                                Comment
                                            </button>
                                        </div>
                                    </div>
                                    <form class='post-comment__form js--pcf$trimmed_post_id' method='post'>
                                            <a href='#' class='post-comment__avatar-link'><img src='$profile_pic' class='post-comment__avatar'></a>
                                            <div class='post-comment__textbox post-comment__textbox--input'>
                                                <span class='post-comment__author'><a href='#' class='post-comment__author-link'>$post_author</a></span>
                                                <div class='post-comment__input js--post-comment__input' role='textbox' aria-multiline='true' contenteditable='true' data-placeholder='Write a comment...'><span class='post-comment__placeholder'>Write a comment...</span><textarea class='post-comment__body js--post-comment__body obj-hidden'></textarea></div>
                                            </div>
                                        </form>
                                  </div>";

                    return $posts_html;
                }

            }               
            
            return "";
        }

        public function loadPosts($user_id, $limit, $start) {
            $enc_user_id = $user_id;
            $user_id = getTrimmedDecodedValue(Constant::$userIdEncKey, $user_id);
            if ( $user_id > 0 ) {
                //SELECT ALL POSTS 
                $sql_query = "SELECT DISTINCT
                                    posts.post_id,
                                    posts.post_author,
                                    posts.post_recipient,
                                    posts.post_body,
                                    posts.date_posted,
                                    posts.date_edited,
                                    posts.post_deleted,
                                    posts.public_post_type,
                                    posts.likes,
                                    relationship.status
                                FROM
                                    posts
                                INNER JOIN 
                                    relationship 
                                ON
                                    ( ((posts.post_author = relationship.user_one_id AND :user_id = relationship.user_two_id) AND relationship.status = 1 ) OR 
                                    ((posts.post_author = relationship.user_two_id AND :user_id = relationship.user_one_id) AND relationship.status = 1 )
                                    OR (posts.post_author = :user_id OR posts.post_recipient = :user_id) ) 
                                ORDER BY 
                                    posts.date_posted DESC 
                                LIMIT $start, $limit";

                $stmt = $this->con->prepare($sql_query);    
                $stmt->execute(array(":user_id"=>$user_id));
                
                //initialize variables that will hold the html tags for posts and comments to be returned
                $posts_html = "";
                $comments_html = "";

                //create an Object that will hold comments for each Post
                $Comments_Obj = new Comment($this->con, false);
                $com_start = 0;
                $com_limit = 4;

                while ($post_entry = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    
                    //retrieve post id and body
                    $post_id = getBase64EncodedValue(Constant::$postEncKey, $post_entry['post_id']);
                    $post_body = $post_entry['post_body'];

                    //check if the post is new or edited
                    $time_label_prefix =  "";
                    if ( $post_entry['date_posted'] == $post_entry['date_edited'] ) {
                        $start_date_time = new DateTime($post_entry['date_posted']);
                    }
                    else {
                        $start_date_time = new DateTime($post_entry['date_edited']);
                        $time_label_prefix =  "edited ";
                    }

                    //calculate time passed after posting
                    $current_date_time = date("Y-m-d H:i:s");
                    // $start_date_time = new DateTime($post_entry['date_posted']);
                    $end_date_time = new DateTime($current_date_time);
                    $date_time_diff = $start_date_time->diff($end_date_time);


                    $time_label = $time_label_prefix . getPostTimeInterval($date_time_diff);

                    //check if current_user liked the current post; add "js--liked" class to like button
                    $Like_Obj = new Like($this->con, $post_entry['post_id'], $user_id);
                    $svg_html = ( $Like_Obj->isLikeEntryExists() ) ? "<svg class='post-footer__icon js--liked'>" : "<svg class='post-footer__icon'>";


                    if ( $post_entry['post_author'] == $user_id ) {
                        //retrieve user's profile pic
                        $profile_pic = "assets/img/uploads/" . $this->User_Obj->profile_pic;
                        //retrieve post author's name
                        $post_author = "{$this->User_Obj->first_name} {$this->User_Obj->last_name}";
                        // var_dump($this->User_Obj);
                    }
                    else {
                        //create new user object for the friend's details
                        $Friend_User_Obj = new User($this->con, $post_entry['post_author']);
                        //retrieve friend's profile pic
                        $profile_pic = "assets/img/uploads/" . $Friend_User_Obj->profile_pic;
                        //retrieve friend's name
                        $post_author = "{$Friend_User_Obj->first_name} {$Friend_User_Obj->last_name}";
                    }
                    
                    //remove the 2 trailing "=" signs in the encrypted $post_id value to be used as a class name
                    $trimmed_post_id = substr_replace($post_id,"",-2);

                    //attached comments for the current post
                    $current_comments = $Comments_Obj->loadComments($trimmed_post_id, $com_start, $com_limit);

                    $posts_html .= "<div class='post-entry' data-pid='$post_id' data-uid='$enc_user_id'>
                                        <div class='post-content'>
                                            <div class='post-header'>
                                                <img src='$profile_pic' class='post-header__img'>
                                                <div class='post-header__details'>
                                                    <span class='post-header__author'>$post_author</span>
                                                    <span class='post-header__date-posted'>$time_label</span>
                                                </div>
                                                <button class='post-header__options-btn'>...</buton>
                                            </div>

                                            <p class='post-body js--post-body'>$post_body</p>
                                            <div class='post-footer'>
                                                <button class='post-footer__link js--like'>
                                                    $svg_html
                                                        <use xlink:href='assets/img/sprite.svg#icon-thumbs-up'></use>
                                                    </svg>
                                                    Like
                                                </button>
                                                <button class='post-footer__link js--comment' aria-controls='post-comment-form'>
                                                    <svg class='post-footer__icon'>
                                                        <use xlink:href='assets/img/sprite.svg#icon-message'></use>
                                                    </svg>
                                                    Comment
                                                </button>
                                            </div>
                                        </div>
                                        $current_comments
                                        <form class='post-comment__form js--comfrm$trimmed_post_id' method='post'>
                                            <a href='#' class='post-comment__avatar-link'><img src='$profile_pic' class='post-comment__avatar'></a>
                                            <div class='post-comment__textbox post-comment__textbox--input'>
                                                <span class='post-comment__author'><a href='#' class='post-comment__author-link'>$post_author</a></span>
                                                <div class='post-comment__input js--post-comment__input' role='textbox' aria-multiline='true' contenteditable='true' data-placeholder='Write a comment...'><span class='post-comment__placeholder'>Write a comment...</span><textarea class='post-comment__body js--post-comment__body obj-hidden'></textarea></div>
                                            </div>
                                        </form>
                                    </div>";
                                    
                                    
                }
                
                return $posts_html;
            }

        }

        public function loadUserFriendsPosts($user_id, $limit, $start) {
            
            if ( $user_id > 0 ) {
                $sql_query = "SELECT * FROM posts WHERE post_author = :user_id OR post_recipient = :post_recipient ORDER BY date_posted DESC LIMIT $start, $limit";
                $stmt = $this->con->prepare($sql_query);    
                $stmt->execute(array(":user_id"=>$user_id, ":post_recipient"=>$user_id));
                
                $posts_html = "";
                while ($post_entry = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    
                    //retrieve post id and body
                    $post_id = getBase64EncodedValue(Constant::$postEncKey, $post_entry['post_id']);
                    $post_body = $post_entry['post_body'];

                    //calculate time passed after posting
                    $current_date_time = date("Y-m-d H:i:s");
                    $start_date_time = new DateTime($post_entry['date_posted']);
                    $end_date_time = new DateTime($current_date_time);
                    $date_time_diff = $start_date_time->diff($end_date_time);


                    $time_label = getPostTimeInterval($date_time_diff);

                    //check if current_user liked the current post; add "js--liked" class to like button
                    $Like_Obj = new Like($this->con, $post_entry['post_id'], $user_id);
                    $svg_html = ( $Like_Obj->isLikeEntryExists() ) ? "<svg class='post-footer__icon js--liked'>" : "<svg class='post-footer__icon'>";

                    //set the post header details based on the post author and post recipient
                    $post_recipient = $post_entry['post_recipient'];

                    
                    //check if the current user is the post recipient
                    if ( $post_recipient == $user_id ) {
                        $post_author_id = $post_entry['post_author'];
                        //create a user object for the post author
                        $Friend_User_Obj = new User($this->con, $post_author_id);
                        //get the post author's details
                        $Friend_User_Obj->getUserInfo($post_author_id);

                        //retrieve user's profile pic
                        $profile_pic = "assets/img/uploads/" . $Friend_User_Obj->profile_pic;
                        //retrieve post author's name
                        $post_author = "{$Friend_User_Obj->first_name} {$Friend_User_Obj->last_name}";
                    }
                    else {
                        //retrieve user's profile pic
                        $profile_pic = "assets/img/uploads/" . $this->User_Obj->profile_pic;
                        //retrieve post author's name
                        $post_author = "{$this->User_Obj->first_name} {$this->User_Obj->last_name}";
                    }

                    //if POST is for user's own wall  craft a POST-ENTRY HTML
                    // if ( $post_recipient == "none" ) {
                        $posts_html .= "<div class='post-entry' data-pid='$post_id'>
                                            <div class='post-header'>
                                                <img src='$profile_pic' class='post-header__img'>
                                                <div class='post-header__details'>
                                                    <span class='post-header__author'>$post_author</span>
                                                    <span class='post-header__date-posted'>$time_label</span>
                                                </div>
                                                <a href='#' class='post-header__edit-btn'>Edit</a>
                                            </div>
                                            <p class='post-body js--post-body'>$post_body</p>
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
                    // }
                }
                
                return $posts_html;
            }

        }

        public function editPost($post_id, $new_content, $user_id) {
            $author_id = $this->User_Obj->user_id;
            // $decrypted_user_id = getTrimmedDecodedValue(Constant::$userIdEncKey, $user_id);
            $decrypted_post_id = getTrimmedDecodedValue(Constant::$postEncKey, $post_id);

            $date_edited = date("Y-m-d H:i:s");

            $sql_update = "UPDATE posts SET post_body = :new_content, date_edited = :date_edited WHERE post_id = :post_id AND post_author = :post_author";
            $stmt = $this->con->prepare($sql_update);
            $stmt->execute(array(":new_content"=> $new_content, ":date_edited"=>$date_edited, ":post_id"=>$decrypted_post_id, ":post_author"=>$author_id));

            if ( $stmt->rowCount() == 1 ) {
                return $date_edited;
            }
            else {
                return "";
            }
        }

        public function deletePost($post_id, $user_id) {
            $author_id = $this->User_Obj->user_id;
            // $decrypted_user_id = getTrimmedDecodedValue(Constant::$userIdEncKey, $user_id);
            $decrypted_post_id = getTrimmedDecodedValue(Constant::$postEncKey, $post_id);

            $date_edited = date("Y-m-d H:i:s");

            $sql_update = "DELETE FROM posts WHERE post_id = :post_id AND post_author = :post_author";
            $stmt = $this->con->prepare($sql_update);
            $stmt->execute(array(":post_id"=>$decrypted_post_id, ":post_author"=>$author_id));

            if ( $stmt->rowCount() == 1 ) {
                return true;
            }
            else {
                return false;
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