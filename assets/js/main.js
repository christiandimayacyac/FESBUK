$(document).ready(function(){

    //LOGIN AND REGISTER FORMS/////////////////////////
    //toggles login and register form
    let switches =  document.querySelectorAll(".js--switch");

    for(let i=0;i<switches.length; i++) {
        switches[i].addEventListener('click',toggleDisplay);
    }

    //EventListeners/////////////////////////////////////

    //Logout EventListener
    if ( document.querySelector(".user-nav__logout") ) {
        document.querySelector(".user-nav__logout").addEventListener('click', () => window.location.href="logout.php");
    }
    
    //Post EventListener
    if ( document.querySelector(".js--wall__submit") ) {
        document.querySelector(".js--wall__submit").addEventListener('click', event => {
            event.preventDefault();
            submitPost();
        });
    }

    

    //Post Options Button EventListener
    $(document).on("click", ".post-header__options-btn", function(event) {
        event.preventDefault();
        // alert("options clicked");
        $("<ul class='post-header__options-menu'><li class='post-header__options-item'>Edit Post</li><li class='post-header__options-item'>Delete Post</li></ul>").insertAfter(event.target)
    });

    //post-header__options-btn EventListener
    $(document).on("focusout", ".post-header__options-btn", function(event){
        $(this).next(".post-header__options-menu").hide();
    });

    $(document).on('keypress','.js--post-comment__input',function(event) {
        let this_input = event.target;
        let hidden_textarea = $(event.target).find(".js--post-comment__body");
        
        if (event.keyCode == 13) {  // detect the enter key
            if ( $(this).text().trim == "" ) {
                event.preventDefault();
            }
            else {
                event.preventDefault();
                $(hidden_textarea).html($(event.target).text());
                let the_comment =   $(hidden_textarea).html();
                let post_id = $(hidden_textarea).closest(".post-entry").attr("data-pid");

                //send an AJAX request to save the new comment
                console.log("to send: " + the_comment + " - " + post_id);
                
                $.post("inc/ajax/fetch-comment-ajax.php",{post_id:post_id,the_comment:the_comment})
                    .done(function(result){
                        if ( result == "" ) {
                            console.log("No Comment");
                        }
                        else {
                            // console.log(result);
                            // $(this_input).closest(".post-entry").append(result);
                            $(result).insertBefore($(this_input).closest(".post-comment__form"));
                            //clear the input textbox
                            $(this_input).text("");
                            $(this_input).blur();
                        }

                    });
                    
            }
            
        }
    });

    $(document).on('keydown','.js--post-comment__input',function(event) {
        let placeholder = $(this).find('.post-comment__placeholder');
        if ( $(this).text() == $(placeholder).text() ) {
            $(placeholder).text("");
        }
    });

    $(document).on('focusout','.js--post-comment__input',function(event) {
        let placeholder = $(this).find('.post-comment__placeholder');
        if ( $(this).text().trim() == "" ) {
            //remove current text
            $(this).text("");  
            //reinsert the deleted/modified html content
            $(this).html("<span class='post-comment__placeholder'>Write a comment...</span><textarea class='post-comment__body js--post-comment__body obj-hidden'></textarea>");  
        }
    });


    //Comment EventListener
    $(document).on("click",".js--comment", function(event) {
        //select the comment button clicked
        let theButton = event.target;
        //get the encrypted post_id value and remove the trailing "=="
        let post_id = $(theButton).closest(".post-entry").attr("data-pid");
        let targetForm = `.js--pcf${post_id}`;
        targetForm = targetForm.replace("==","");

        //toggle the comment_form and comment posts of the selected post
        if ( $(targetForm).css("display") == "none" ) {
            $(targetForm).css("display","flex");
            //set the cursor inside the textbox
            $(targetForm).find(".js--post-comment__input").focus();
            $(targetForm).closest(".post-entry").find("[class*='js--pc']").show();
        }
        else {
            $(targetForm).css("display","none");
            $(targetForm).closest(".post-entry").find("[class*='js--pc']").hide();
        }
        
    });

    //POSTS////////////////////////////////////////////
    let start = 0;
    let limit = 7;
    let ready_to_fetch = true;

    //function that sends ajax request to fetch-posts-ajax.php to pull posts from the database
    function loadUserPosts() {
        // console.log("loading user posts");
        $.post("inc/ajax/fetch-posts-ajax.php",{start:start, limit:limit})
            .done(function(result){
                if ( result == "" ) {
                    ready_to_fetch = false;
                    $(".loading__info").hide();
                }
                else {
                    // console.log(result);
                    ready_to_fetch = true;
                    $(".wall__posts").append(result);
                }
            });
    }
    if ( ready_to_fetch ) {
        ready_to_fetch = false;
        loadUserPosts();
    }

    $(window).scroll(function(){
        if ( ($(window).scrollTop() + $(window).height() > $(".wall").height() + $("header").height() + 50) && ready_to_fetch == true ) {
            
            $(".loading__info").show();
            start = start + limit;
            setTimeout(function(){
                loadUserPosts();
            }, 1000);
            ready_to_fetch = false;
        }
        else if ( ($(window).scrollTop() + $(window).height() > $(".wall").height() + $("header").height() + 50) && ready_to_fetch == false  ){
            // console.log("scrolling");
        }
    });
    
});
    

$(document).on("click", ".js--like", () =>{
    $(event.target).find(".post-footer__icon").toggleClass("js--liked");

    let theButton = event.target;
    
    $post_id = $(theButton).closest(".post-entry").attr("data-pid");
    
    //get the number of likes in the user details
    let num_likes = $(".user-details__num_likes").text();

    if ( $(theButton).children(".post-footer__icon").hasClass("js--liked") ) {
        

        $.post("inc/ajax/likes-ajax.php",{like_flag: true, post_id:$post_id, operation:"insert"})
            .done(function(result){
                if (result) {
                    //extract the number and convert to int
                    updatePostLike(num_likes,"likes", "increment");
                    
                }
        });
    }
    else {
        // console.log("unliked:" + $post_id);
        $.post("inc/ajax/likes-ajax.php",{like_flag: true, post_id:$post_id, operation:"delete"})
            .done(function(result){
                if (result) {
                    //extract the number and convert to int
                    updatePostLike(num_likes,"likes", "decrement");
                    
                }
        });
    }
});





//retrieve the comment details and submit the form
function postComment() {
    let hidden_textbox = $(".js--post-comment__body");
}


//update the posts and like stats in the user-details area
function updatePostLike($el, $type, $operation) {
    if ( $type == "likes" ) {
        $likes = parseInt($el.split(":").pop());
        ($operation == "increment") ? $likes++ : $likes--;
        $(".user-details__num_likes").text("Likes: " + $likes);
    }
    else {
        $posts = parseInt($el.split(":").pop());
        ($operation == "increment") ? $posts++ : $posts--;
        $(".user-details__num_posts").text("Posts: " + $posts);
    }
}


function submitPost(event) {
    let post_body = $(".js--wall__textarea").val();
    let user_id = $(".js--wall__input").val();

    // console.log("to send: " + post_body + " - " + user_id);

    $.post("inc/ajax/posts-ajax.php",{user_id:user_id, post_body:post_body})
    .done(function(result){
        //check if a post-entry exists; append to it if a post entry exists; otherwise insert before the loading_info div
        if ( document.querySelector(".post-entry") != null ) {
            $(result).insertAfter($(".post-entry").last(".post-comment__form"));
        }
        else {
            $(".wall__posts").append(result);
        }

        //get the number of likes in the user details
        let num_posts = $(".user-details__num_posts").text();
        updatePostLike(num_posts,"posts", "increment")

        $(".js--wall__textarea").val("");
        
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





