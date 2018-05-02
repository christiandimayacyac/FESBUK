<?php include "inc/header.php"; ?>
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
                            <span class="user-details__first-name">Christian</span>
                            <span class="user-details__last-name">Dimayacyac</span>
                        </div>
                        <span class="user-details__num-post">Posts: 12</span>
                        <span class="user-details__likes">Likes: 4</span>
                    </div>
                </div>
                <div class="sidebar-links">
                    <h3 class="sidebar__title">Popular</h3>
                    <ul class="sidebar__list">
                        <li class="sidebar__link"><a href="#">NBA</li>
                        <li class="sidebar__link"><a href="#">Baking</a></li>
                        <li class="sidebar__link"><a href="#">Technology</a></li>
                        <li class="sidebar__link"><a href="#">Web Development</a></li>
                        <li class="sidebar__link"><a href="#">Lifestyle</a></li>
                        <li class="sidebar__link"><a href="#">Politics</a></li>
                    </ul>
                </div>
            </div>
            <main class="main">
                Welcome <?php echo ($_SESSION['user_name']); ?> <a href="logout.php">Logout</a>
            </main>
        </div>
    </div>
</div>

<?php include "inc/footer.php"; ?>