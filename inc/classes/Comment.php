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

        public function __construct($con, $new_comment = true) {
            $this->con = $con;
            $this->new_comment = $new_comment;
        }

        public function insertComment($comment_author, $comment_body, $post_id) {

            //check if is new comment to insert
            if ( $this->new_comment ) {
                $this->date_posted = date("Y-m-d H:i:s");
                $this->date_edited = $this->date_posted;

                try{
                    $sql_insert = "INSERT INTO comments (comment_author, comment_body, post_id, date_posted, date_edited) VALUES (:comment_author, :comment_body, :post_id, :date_posted, :date_edited)";
                    $stmt = $this->con->prepare($sql_insert);
                    $stmt->execute(array(':comment_author'=>$comment_author, ':comment_body'=>$comment_body, ':post_id'=>$post_id, ':date_posted'=>$this->date_posted, ':date_edited'=>$this->date_edited));
                 }
                 catch(PDOException $ex){
                    //throw error
                    return false;
                 }
            }
            else {
                //get the date_posted value
            }

            
             
             return true;

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