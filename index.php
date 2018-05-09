<?php 
    include "inc/header.php"; 

    //create new User Object to access current user infos 
    $Cur_user = new User($con, $_SESSION['user_id']);
    $User_posts = new Post($con, $_SESSION['user_id']);
?>
<div class="container">
    <div class="row">
        <div class="content">
            <div class="sidebar">
                <div class="user-info">
                    <div class="user-info__pix-box">
                        <img src="assets/img/base/default.png" alt="profile picture" class="user-info__pix">
                    </div>
                    <div class="user-details">
                        <div class="user-details__fullname">
                            <span class="user-details__first-name"><a href="<?php echo $Cur_user->first_name; ?>" class="user-details__link"><?php echo $Cur_user->first_name; ?></a></span>
                            <span class="user-details__last-name"><a href="<?php echo $Cur_user->last_name; ?>" class="user-details__link"><?php echo $Cur_user->last_name; ?></a></span>
                        </div>
                        <span class="user-details__num_posts">
                            Posts: <?php echo $Cur_user->num_posts;?>
                        </span>
                        <span class="user-details__num_likes">Likes: <?php echo $Cur_user->num_likes;?></span>
                    </div>
                </div>
                <div class="sidebar-links">
                    <h3 class="sidebar__title">Popular</h3>
                    <ul class="sidebar__list">
                        <li class="sidebar__item"><a href="#" class="sidebar__link">NBA</a?</li>
                        <li class="sidebar__item"><a href="#" class="sidebar__link">Baking</a></li>
                        <li class="sidebar__item"><a href="#" class="sidebar__link">Technology</a></li>
                        <li class="sidebar__item"><a href="#" class="sidebar__link">Web Development</a></li>
                        <li class="sidebar__item"><a href="#" class="sidebar__link">Lifestyle</a></li>
                        <li class="sidebar__item"><a href="#" class="sidebar__link">Politics</a></li>
                    </ul>
                </div>
            </div>
            <main class="main">
                Welcome <?php echo ($_SESSION['user_name']); ?>
                <?php var_dump($_SESSION); ?>
                <div class="wall">
                    <form action="#" class="wall__form">
                        <textarea class="wall__textarea js--wall__textarea" name="wall__textarea" placeholder="What do you have in mind today?"></textarea>
                        <input type="text" class="js--wall__input" value="<?php echo $Cur_user->user_id; ?>" hidden>
                        <button class="wall__submit js--wall__submit" name="wall__submit">Post</button>
                    </form>
                    <?php $User_posts->loadPosts($_SESSION['user_id']); ?>
                </div>


            </main>
        </div>
    </div>
</div>

<?php include "inc/footer.php";?>