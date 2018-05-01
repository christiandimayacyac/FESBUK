<?php
    class Page {

        public static function ForceLogin() {
            //check if sessions are set
            if ( !isset($_SESSION['user_id']) && !isset($_SESSION['user_name']) ) {
                //checks if session is set and valid
                if ( isset($_COOKIE['rememberFesbuk']) && !isCookieValid() ) {

                    redirectTo('logout');
                    return;
                }
                elseif ( isset($_COOKIE['rememberFesbuk']) && isCookieValid() ) {
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
            if ( !isset($_SESSION['user_id']) && !isset($_SESSION['user_name']) ) {
                redirectTo('logout');
            }
        }

    }

?>