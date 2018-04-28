<?php

    if ( isset($_POST['reg_submit']) ) { 
        //validate all the data submitted
        $Account = new User($con);

        //Collect data from POST
        $user_info = array (
                            "Username" => $_POST['reg_user_name'],
                            "First name" => $_POST['reg_first_name'],
                            "Last name" => $_POST['reg_last_name'],
                            "Email" => $_POST['reg_email'],
                            "Password 1" => $_POST['reg_password1'],
                            "Password 2" => $_POST['reg_password2']
                            );

        //validate POST data
        $isValid = $Account->validateAll($user_info);
    }


?>