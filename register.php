<?php
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    require_once "inc/config.php";


    //include handler for this page
    include_once "inc/handlers/register-handler.php";

    // Page::ForceLogin();

    // $User = new User($_SESSION['user_id']);
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="assets/css/style.css">        
        <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900" rel="stylesheet">

        <link rel="stylesheet" href="assets/css/style.css">
        <!-- <link rel="shortcut icon" type="image/png" href="img/favicon.png"> -->
        
        <title>Fesbuk - The Social Site of 2018</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-1-of-1">
                    <div class="form-container">
                        <h1 class="heading-1 u-margin-bottom-s form-title">Register</h1>
                        <form action="register.php" class="register-form" method="POST">
                            
                                <?php
                                    if ( isset($_POST['reg_submit']) ) {

                                        if ( $isValid ) {
                                            //insert data to database
                                            //redirect to index/dashboard
                                            // echo $isValid;
                                        }

                                        else {
                                            // var_dump($Account->errors_array);
                                            $form_errors = $Account->getErrors();
                                            echo "<div class='register-form__errors u-margin-bottom-s'><h2 class='register-form__title'>Errors found:</h2><ul class='register-form__list'>";
    
                                                foreach ($form_errors[0] as $form_err) {
                                                    echo "<li class='register-form__item'>$form_err</li>";
                                                }
    
                                            echo "</ul></div>";
                                            //show errors
                                            //redisplay the data to their corresponding input
                                        }

                                    }
                                    
                                   
                                    
                                ?>
                                
                            <div class="register-form__group">
                                <input type="text" class="register-form__input" name="reg_user_name" id="reg_user_name" placeholder="User Name">
                                <label for="reg_user_name" class="register-form__label">First Name</label>
                            </div>
                            <div class="register-form__group">
                                <input type="text" class="register-form__input" name="reg_first_name" id="reg_first_name" placeholder="First Name">
                                <label for="reg_first_name" class="register-form__label">First Name</label>
                            </div>
                            <div class="register-form__group">
                                <input type="text" class="register-form__input" name="reg_last_name" id="reg_last_name" placeholder="Last Name">
                                <label for="reg_last_name" class="register-form__label">Last Name</label>
                            </div>
                            <div class="register-form__group">
                                <input type="email" class="register-form__input" name="reg_email" id="reg_email" placeholder="Email">
                                <label for="reg_email" class="register-form__label">Email</label>
                            </div>
                            <div class="register-form__group">
                                <input type="password" class="register-form__input" name="reg_password1" id="reg_password1" placeholder="Password">
                                <label for="reg_password1" class="register-form__label">Password</label>
                            </div>
                            <div class="register-form__group">
                                <input type="password" class="register-form__input" name="reg_password2" id="reg_password2" placeholder="Confirm Password">
                                <label for="reg_password2" class="register-form__label">Confirm Password</label>
                            </div>
                            <!-- <div class="register-form__group">
                                <input type="checkbox" class="register-form__chkbox" name="reg_chkbox">
                                <label for="reg_chkbox" class="register-form__label">Remember me</label>
                            </div> -->
                            <button type="submit" class="btn register-form__btn js--btn-signup" name="reg_submit">Sign Up</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<?php include_once "inc/footer.php"; ?>
