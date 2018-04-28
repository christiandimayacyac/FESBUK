<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }

    class DB {
        protected static $con;
        protected static $dsn = "mysql:charset=utf8; host=localhost; dbname=db_fesbuk";
        protected static $db_user_name = "ziemdiadmin";
        protected static $db_user_password = "ziemdiadmin";

        private function __construnct() {
            
            try {
                self::$con = new PDO(self::$dsn, self::$db_user_name, self::$db_user_password);
                self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$con->setAttribute(PDO::ATTR_PERSISTENT, false);
            }
            catch(PDO $ex) {
                echo "Error connecting to the Database. Please contact the Site Admin.";
                exit;
            }
            
        }

        public static function getConnection() {
            //check if an Instance already exist
            if ( !self::$con ) {
                new DB();
            }

            //return a connection Object
            return self::$con;
        }
    }


?>