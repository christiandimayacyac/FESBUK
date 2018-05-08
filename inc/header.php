<?php
    ob_start();

    // define the __CONFIG__ to allow the config.php file
    define('__CONFIG__', true);
    require_once "inc/config.php";

    Page::ForceLogout();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Document</title>
</head>
<body>
    <header class="header">
        <!-- <img src="assets/img/base/newlogo.png" alt="fesbuk logo" class="logo"> -->
        <h1 class="header__logo">Fesbuk</h1>
        <form action="#" class="search">
            <input type="text" class="search__input" placeholder="Search">
            <button class="search__btn">
                <svg class="search__icon">
                    <use xlink:href="assets/img/sprite.svg#icon-magnifying-glass"></use>
                </svg>
            </button>
        </form>
        <nav class="user-nav">
            <span class="user-nav__username"><a href="<?php echo ( isset($_SESSION['user_name']) ) ? $_SESSION['user_name'] : ''; ?>" class="user-nav__link"><?php echo ( isset($_SESSION['user_name']) ) ? $_SESSION['user_name'] : ''; ?></a></span>
            <!-- <div class="user-nav__icon-box">
                <svg class="user-nav__icon">
                    <use xlink:href="assets/img/sprite.svg#icon-magnifying-glass"></use>
                </svg>
            </div> -->
            <div class="user-nav__icon-box">
                <svg class="user-nav__icon">
                    <use xlink:href="assets/img/sprite.svg#icon-home"></use>
                </svg>
            </div>
            <div class="user-nav__icon-box">
                <svg class="user-nav__icon">
                    <use xlink:href="assets/img/sprite.svg#icon-users"></use>
                </svg>
            </div>
            <div class="user-nav__icon-box">
                <svg class="user-nav__icon">
                    <use xlink:href="assets/img/sprite.svg#icon-mail"></use>
                </svg>
            </div>
            <div class="user-nav__icon-box">
                <svg class="user-nav__icon">
                    <use xlink:href="assets/img/sprite.svg#icon-bell"></use>
                </svg>
            </div>
            <div class="user-nav__icon-box">
                <svg class="user-nav__icon">
                    <use xlink:href="assets/img/sprite.svg#icon-cog"></use>
                </svg>
            </div>
            <button class="user-nav__logout">Logout</button>
        </nav>
    </header>