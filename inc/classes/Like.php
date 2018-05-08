<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }

    class Like {

        private $con;
        private $user_id;
        private $post_id;

        public function __construct($con, $post_id, $user_id) {
            $this->con = $con;
            $this->user_id = $user_id;
            $this->post_id = $post_id;
        }

        public function insertLike() {

            try{
                $sql_insert = "INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)";
                $stmt = $this->con->prepare($sql_insert);
                $stmt->execute(array(':user_id'=>$this->user_id, ':post_id'=>$this->post_id));
             }
             catch(PDOException $ex){
                //throw error
                return false;
             }
             
             return true;

        }

        public function deleteLike() {

            try{
                $sql_delete = "DELETE FROM likes WHERE user_id = :user_id AND  post_id = :post_id LIMIT 1";
                $stmt = $this->con->prepare($sql_delete);
                $stmt->execute(array(':user_id'=>$this->user_id, ':post_id'=>$this->post_id));
             }
             catch(PDOException $ex){
                //throw error
                return false;
             }
             
             return true;
        }

        public function isLikeEntryExists() {
            $sql_query = "SELECT * FROM likes WHERE post_id = :post_id AND user_id = :user_id LIMIT 1";
            $stmt = $this->con->prepare($sql_query);
            $stmt->execute(array(":post_id"=>$this->post_id, ":user_id"=>$this->user_id));

            if ( $stmt->rowCount() == 1 ) {
                return true;
            }
            else {
                return false;
            }
        }

        public function getUserId() {
            return $this->user_id;
        }
        
        public function getPostId() {
            return $this->post_id;
        }

    }

?>