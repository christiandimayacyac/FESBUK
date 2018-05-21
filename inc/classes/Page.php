<?php
    // if __CONFIG__ is not defined, do not load this file
    if( !defined('__CONFIG__') ) {
        exit('Config File is not defined.');
    }
    class Page {
        protected static $con;
        

        public static function ForceLogin() {
            $con = DB::getConnection();
            //check if sessions are set
            if ( !isset($_SESSION['user_id']) && !isset($_SESSION['user_name']) ) {
                //checks if session is set and valid
                if ( isset($_COOKIE['rememberFesbuk']) && !isCookieValid($con) ) {
                    redirectTo('logout');
                    return;
                }
                elseif ( isset($_COOKIE['rememberFesbuk']) && isCookieValid($con) ) {
                    redirectTo('index');
                    return;
                }
                else {
                    return;
                }
            }
            redirectTo('index');
            return;
        }

        public static function ForceLogout() {
            $con = DB::getConnection();
            if ( !isset($_SESSION['user_id']) && !isset($_SESSION['user_name']) || (isset($_COOKIE['rememberFesbuk']) && !isCookieValid($con)) ) {
                redirectTo('logout');
            }
        }

    }

?>