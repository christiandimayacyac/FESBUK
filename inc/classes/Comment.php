<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }

    class Comment {

        private $con;
        private $new_comment;
        private $comment_author;
        private $comment_body;
        private $post_id;
        private $date_posted;
        private $date_edited;
        private $is_deleted;
        public $operation_status;
        public $User_Obj;

        public function __construct($con, $new_comment = true) {
            $this->con = $con;
            $this->new_comment = $new_comment;
        }

        public function insertComment($comment_author, $comment_body, $post_id) {
            $comment_html = "";

            //check if is new comment to insert
            if ( $this->new_comment ) {
                $this->date_posted = date("Y-m-d H:i:s");
                $this->date_edited = $this->date_posted;
                $this->User_Obj = new User($this->con, $comment_author);
                $this->User_Obj->getUserInfo($comment_author);
                //retrieve user's profile pic
                $profile_pic = "assets/img/uploads/" . $this->User_Obj->profile_pic;
                //retrieve post author's name
                $comment_author = "{$this->User_Obj->first_name} {$this->User_Obj->last_name}";

                try{
                    $sql_insert = "INSERT INTO comments (comment_author, comment_body, post_id, date_posted, date_edited) VALUES (:comment_author, :comment_body, :post_id, :date_posted, :date_edited)";
                    $stmt = $this->con->prepare($sql_insert);
                    $stmt->execute(array(':comment_author'=>$comment_author, ':comment_body'=>$comment_body, ':post_id'=>$post_id, ':date_posted'=>$this->date_posted, ':date_edited'=>$this->date_edited));

                    if ( $stmt->rowCount() == 1 ) {
                        $comment_id = $this->con->lastInsertId();
                        //craft a post comment html to be attached to the commented post
                        $comment_html = "<div class='post-comment__entry js--pc$comment_id' method='post'>
                                            <a href='#' class='post-comment__avatar-link'><img src='$profile_pic' class='post-comment__avatar'></a>
                                            <div class='post-comment__text'>
                                                
                                                <div class='post-comment__input js--post-comment__input' role='textbox' aria-multiline='true' contenteditable='false'><textarea class='post-comment__body js--post-comment__body obj-hidden'>$comment_body</textarea><span class='post-comment__author'><a href='#' class='post-comment__author-link--display'>$comment_author</a></span>&nbsp;$comment_body</div>
                                            </div>
                                            <div class='post-comment__option-btn'>...</div>
                                        </div>
                                    </div>";
                    }
                 }
                 catch(PDOException $ex){
                    //throw error
                    // return false;
                 }
            }
            else {
                //get the date_posted value
            }

            
             
             return $comment_html;

        }

        public function loadComments($post_id) {
            $comments_html = "";

            if ( ($post_id > 0 && $post_author > 0) || 1 == 1 ) {
                $sql_query = "SELECT * FROM comments WHERE post_id = :post_id";
                $stmt = $this->con->prepare($sql_query);
                $stmt->execute(array(":post_id"=>$post_id) );

                while ( $rs = $stmt->fetch(PDO::FETCH_ASSOC) ) {
                    // $comments_array = array_merge($comments_array, $rs);
                    // if ( !empty($current_comments) ) {
                        // foreach ( $current_comments as $current_comment ) {
                            $comment_id = getBase64EncodedValue(Constant::$commentEncKey, $rs['comment_id']);
                            $time_label =  $rs['date_posted'];
                            $comment_body =  $rs['comment_body'];
                            $trimmed_comment_id = substr_replace($comment_id,"",-2);

                            //Create a user object to get the current User Profile Pic and Name of the comment author
                            $this->User_Obj = new User($this->con, $rs['comment_author']);
                            //get the comment author details
                            $profile_pic = $this->User_Obj->profile_pic; 
                            $comment_author = "{$this->User_Obj->first_name} {$this->User_Obj->last_name}";
                            $this->User_Obj->profile_pic; 
                            $this->User_Obj->profile_pic; 


                            $comments_html.= "<div class='comment-entry' data-cid='$comment_id'>
                                                <div class='comment-content'>
                                                    <div class='comment-header'>
                                                        <img src='$profile_pic' class='comment-header__img'>
                                                        <div class='comment-header__details'>
                                                            <span class='comment-header__author'>$comment_author</span>
                                                            <span class='comment-header__date-posted'>$time_label</span>
                                                        </div>
                                                        <button class='comment-header__options-btn'>...</buton>
                                                    </div>

                                                    <p class='comment-body'>$comment_body</p>
                                                    
                                            </div>


                                            
                            
                            ";
                            var_dump($comments_html);

                            // <div class='comment-footer'>
                            //             <button class='comment-footer__link js--like'>
                            //                 $svg_html
                            //                     <use xlink:href='assets/img/sprite.svg#icon-thumbs-up'></use>
                            //                 </svg>
                            //                 Like
                            //             </button>
                            //             <button class='comment-footer__link js--comment' aria-controls='comment-form'>
                            //                 <svg class='comment-footer__icon'>
                            //                     <use xlink:href='assets/img/sprite.svg#icon-message'></use>
                            //                 </svg>
                            //                 Comment
                            //             </button>
                            //         </div>
                            //     </div>
                            //     <form class='comment__form js--pcf$trimmed_comment_id' method='post'>
                            //         <a href='#' class='comment__avatar-link'><img src='$profile_pic' class='comment__avatar'></a>
                            //         <div class='post-comment__textbox'>
                            //             <span class='comment__author'><a href='#' class='comment__author-link'>$comment_author</a></span>
                            //             <div class='comment__input js--comment__input' role='textbox' aria-multiline='true' contenteditable='true' data-placeholder='Write a comment...'><span class='post-comment__placeholder'>Write a comment...</span><textarea class='post-comment__body js--post-comment__body obj-hidden'></textarea></div>
                            //         </div>
                            //     </form>



                        // }
                    // } //end if
                    unset($this->User_Obj);
                }
            }

            return $comments_html;
        }

        // public function deleteComment() {

        //     try{
        //         $sql_delete = "DELETE FROM likes WHERE user_id = :user_id AND  post_id = :post_id LIMIT 1";
        //         $stmt = $this->con->prepare($sql_delete);
        //         $stmt->execute(array(':user_id'=>$this->user_id, ':post_id'=>$this->post_id));
        //      }
        //      catch(PDOException $ex){
        //         //throw error
        //         return false;
        //      }
             
        //      return true;
        // }

        // public function isCommentEntryExists() {
        //     $sql_query = "SELECT * FROM likes WHERE post_id = :post_id AND user_id = :user_id LIMIT 1";
        //     $stmt = $this->con->prepare($sql_query);
        //     $stmt->execute(array(":post_id"=>$this->post_id, ":user_id"=>$this->user_id));

        //     if ( $stmt->rowCount() == 1 ) {
        //         return true;
        //     }
        //     else {
        //         return false;
        //     }
        // }

        // public function getUserId() {
        //     return $this->user_id;
        // }
        
        // public function getPostId() {
        //     return $this->post_id;
        // }

    }

?>