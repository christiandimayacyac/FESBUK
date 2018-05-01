<?php
    ob_start();
    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    require_once "inc/config.php";


    //include handler for this page
    include_once "inc/handlers/register-handler.php";
    include_once "inc/handlers/login-handler.php";

    Page::ForceLogin();

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
        <link rel="shortcut icon" type="image/png" href="img/favicon.png">
        
        <title>Fesbuk - The Social Site of 2018</title>
    </head>
    <body>
       
            <main class="main-content">
                    <img src="assets/img/base/register-bg.jpg" class="main-content__bg" alt="desk top">
                    <div class="form-container">
                        <h1 class="heading-1 u-margin-bottom-s form-title js--form-title"><?php echo ( isset($_POST['reg_submit']) ) ? 'Register' : 'Login'; ?></h1>

                            <!-- Erro list container -->
                            <?php
                                if ( isset($_POST['reg_submit']) || isset($_POST['login_submit']) ) {
                                    if ( !$isValid || !$login_success) {
                                
                                        $form_errors = $Account->getErrors();
                                        echo "<div class='form-errors'><h2 class='form-errors__title'>Errors found:</h2><ul class='form-errors__list'>";

                                            foreach($form_errors as $form_err) {
                                                echo "<li class='form-errors__item'>$form_err</li>";
                                            }

                                        echo "</ul></div>";
                                    }

                                }
                                
                            ?>
                        <!-- <?php echo ( isset($_POST['reg_submit']) ) ? 'register clicked' : 'register not clicked'; ?> -->
                        <form action="register.php" class="login-form <?php echo ( isset($_POST['reg_submit']) ) ? 'u-hidden' : 'u-show'; ?> js--form" method="POST">
                            <div class="login-form__group">
                                <input type="text" class="login-form__input" name="login_user_name" id="login_user_name" value="<?php echo getPrevValue('login_user_name'); ?>" placeholder="User Name" required>
                                <label for="login_user_name" class="login-form__label">User Name</label>
                            </div>
                            <div class="login-form__group">
                                <input type="password" class="login-form__input" name="login_password" id="login_password" placeholder="Password" required>
                                <label for="login_password" class="login-form__label">Password</label>
                            </div>
                            <div class="login-form__group">
                                <input type="checkbox" class="login-form__chkbox" name="login_chkbox" id="login_chkbox">
                                <label for="login_chkbox" class="login-form__label" value="yes">Keep me logged in</label>
                            </div>
                            <button type="submit" class="btn login-form__btn js--btn-signup" name="login_submit">Sign in</button>
                            <span class="login-form__footer">Don't have an account yet? <a href="#" class="login-form__switch-btn js--switch">Sign Up</a></span>
                        </form>


                        <form action="register.php" class="register-form <?php echo ( isset($_POST['login_submit']) || (!isset($_POST['login_submit']) && !isset($_POST['reg_submit'])) ) ? 'u-hidden' : ''; ?> js--form" method="POST">
                            <div class="register-form__group">
                                <input type="text" class="register-form__input" name="reg_user_name" id="reg_user_name" value="<?php echo getPrevValue('reg_user_name'); ?>" placeholder="User Name" required>
                                <label for="reg_user_name" class="register-form__label">User Name</label>
                            </div>
                            <div class="register-form__group">
                                <input type="text" class="register-form__input" name="reg_first_name" id="reg_first_name" value="<?php echo getPrevValue('reg_first_name'); ?>" placeholder="First Name" required>
                                <label for="reg_first_name" class="register-form__label">First Name</label>
                            </div>
                            <div class="register-form__group">
                                <input type="text" class="register-form__input" name="reg_last_name" id="reg_last_name" value="<?php echo getPrevValue('reg_last_name'); ?>" placeholder="Last Name" required>
                                <label for="reg_last_name" class="register-form__label">Last Name</label>
                            </div>
                            <div class="register-form__group">
                                <input type="email" class="register-form__input" name="reg_email" id="reg_email" value="<?php echo getPrevValue('reg_email'); ?>" placeholder="Email" required>
                                <label for="reg_email" class="register-form__label">Email</label>
                            </div>
                            <div class="register-form__group">
                                <input type="password" class="register-form__input" name="reg_password1" id="reg_password1" placeholder="Password" required>
                                <label for="reg_password1" class="register-form__label">Password</label>
                            </div>
                            <div class="register-form__group">
                                <input type="password" class="register-form__input" name="reg_password2" id="reg_password2" placeholder="Confirm Password" required>
                                <label for="reg_password2" class="register-form__label">Confirm Password</label>
                            </div>
                            <button type="submit" class="btn register-form__btn js--btn-signup" name="reg_submit">Sign Up</button>
                            <span class="register-form__footer">Already have an account? <a href="#" class="reg-form__switch-btn js--switch">Sign In</a></span>
                        </form>
                    </div>
                
            </main>


<?php 
    include_once "inc/footer.php"; 
    ob_end_flush();
?>
