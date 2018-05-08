document.addEventListener('DOMContentLoaded', function() {
    //toggles login and register form
    let switches =  document.querySelectorAll(".js--switch");

    for(let i=0;i<switches.length; i++) {
        switches[i].addEventListener('click',toggleDisplay);
    }

    //EventListeners/////////////////////////////////////

    //Logout EventListener
    document.querySelector(".user-nav__logout").addEventListener('click', () => window.location.href="logout.php");

    //Post EventListener
    document.querySelector(".js--wall__submit").addEventListener('click', event => {
        event.preventDefault();
        submitPost();
    });

    //Like EventListener
    // document.querySelector(".js--like").addEventListener('click', event => {
    //     event.preventDefault();
    //     $(event.target).children(".post-footer__icon").toggleClass("js--liked");
    //     // setLike(event);
    // });


});

$(document).on("click", ".js--like", () =>{
    $(event.target).children(".post-footer__icon").toggleClass("js--liked");

    let theButton = event.target;
    
    $post_id = $(theButton).closest(".post-entry").attr("data-pid");
    

    if ( $(theButton).children(".post-footer__icon").hasClass("js--liked") ) {
        // alert("liked the post");
        // console.log("liked:" + $post_id);
        $.post("inc/ajax/likes-ajax.php",{like_flag: true, post_id:$post_id, operation:"insert"})
            .done(function(result){
                // console.log(result);
        });
    }
    else {
        // console.log("unliked:" + $post_id);
        $.post("inc/ajax/likes-ajax.php",{like_flag: true, post_id:$post_id, operation:"delete"})
            .done(function(result){
                // console.log(result);
        });
    }
});

function setLike(event) {
    // console.log(event.target);
    // console.log($(event.target)[0].classList[1]);
    console.log($(event.target));
}

function submitPost(event) {
    let post_body = $(".js--wall__textarea").val();
    let user_id = $(".js--wall__input").val();

    $.post("inc/ajax/posts-ajax.php",{user_id:user_id, post_body:post_body})
    .done(function(result){
        $(result).insertAfter(".wall__form");
    });
}

function toggleDisplay(event) {
    event.preventDefault();
    //select the Login and Register Forms
    let forms = document.querySelectorAll('.js--form');
    let form_title = document.querySelector(".js--form-title");

    //toggle the .u-hidden as the user clicks any of the 2 .js--switch
    for ( let frm=0;frm<forms.length; frm++ ) {
        forms[frm].classList.toggle("u-hidden");
    }

    if ( form_title.innerHTML === "Login" ) {
        form_title.innerHTML = "Register";
    }
    else {
        form_title.innerHTML = "Login";
    }

    //select the error list container
    err_container = document.querySelector(".form-errors");

    //hide the form errors everytime the user switched between Login and Register
    if ( err_container != null) {
        err_container.style.display = "none";
    }
    
    
}





