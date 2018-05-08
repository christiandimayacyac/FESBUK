<?php 
    include "inc/header.php"; 

    //create new User Object to access current user infos 
    $Cur_user = new User($con, $_SESSION['user_id']);
?>
<div class="container">
    <div class="row">
        <div class="content">
            <h1>This is the profile page... Under Construction...</h1>
        </div>
    </div>
</div>

<?php include "inc/footer.php"; ?>