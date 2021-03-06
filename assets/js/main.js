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

        //check if options menu exists in the DOM; insert if not otherwise display it
        if ( $(this).parent().find(".js--post-header__options-menu").text() == "" ) {
            $("<ul class='post-header__options-menu js--post-header__options-menu'><li class='post-header__options-item js--post-header__options-item'>Edit Post</li><li class='post-header__options-item js--post-header__options-item'>Delete Post</li></ul>").insertAfter(event.target)
        }
        else{
            $(this).parent().find(".js--post-header__options-menu").show()
        } 
    });

    //Post Options Button Item EventListener - Edit / Delete
    $(document).on("click", ".js--post-header__options-item", function(event) {
        event.preventDefault();
        if ( $(this).text() == "Edit Post" ) {            
            //check if textarea is already displayed; don't redisplay if already in the DOM
            if ( $(this).closest(".post-content").find(".js--post-edit__container").length < 1 ) {
                let post_body =  $(this).closest(".post-entry").find(".js--post-body");
                $(post_body).hide();
                let textarea_html = "<div class='post-edit__container js--post-edit__container'><textarea class='post-edit__textarea js--post-edit__textarea js--auto-expand' rows='1' data-min-rows='1' >" + $(post_body).text() + "</textarea><div class='post-edit__buttons'><button class='post-edit__cancel js--post-edit__cancel'>Cancel</button><button class='post-edit__done js--post-edit__done'>Done Editing</button</div></div>";
                $(textarea_html).insertAfter($(this).closest(".post-header"));
            }
            else {
                return;
            }

        }
        //show Modal Window for POST DELETE
        else{
            //get Post ID to be used in the modal window attribute
            let post_id = $(this).closest(".post-entry").attr("data-pid");
            let user_id = $(this).closest(".post-entry").attr("data-uid");

            $(this).closest(".post-entry").find(".js--post-body").show();
            $(".overlay").css("display","flex");
            $(".overlay .modal__title").text("Delete Post");
            $(".overlay .modal__body").text("Are you sure you want to delete this post?");
            $(".overlay .modal__footer-text").text("");
            $(".overlay .modal").attr({"data-pid": post_id, "data-uid": user_id});
        }
       
    });

    //Modal EventListener - [Cancel] / Delete
    $(document).on("click", ".modal__footer-buttons--1", function(){
        let modal_type = $(".modal").attr("data-modal-type");
        if ( $(this).text() == "Cancel" ) {
            $(".overlay .modal").removeAttr("data-pid");
            $(".overlay").css("display","none");
        } 
    });

    //Modal EventListener - Cancel / [Delete]
    $(document).on("click", ".modal__footer-buttons--2", function(){
        let modal_type = $(".modal").attr("data-modal-type");
        if ( $(this).text() == "Delete" ) {
            $(".overlay").css("display","none");

            //Delete Post in the Database through AJAX
            let post_id = $(".modal").attr("data-pid");
            let user_id = $(".modal").attr("data-uid");
            $.post("inc/ajax/posts-ajax.php",{post_id:post_id, user_id: user_id, operation:"delete"})
                .done(function(result){
                    if ( result ) {
                        // $("[data-pid='" + post_id + "']").hide(); // for ES5 and earlier versions
                        $(`[data-pid="${post_id}"]`).hide();
                    }
                });
        } 
    });



    //Post Edit Buttons EventListener -  Cancel / Done Editing
    $(document).on("click", ".js--post-edit__cancel", function(){
        $(this).closest(".post-entry").find(".js--post-body").show();
        $(this).closest(".js--post-edit__container").remove();
    }); 

    $(document).on("click", ".js--post-edit__done", function(){
        let time_label = $(this).closest(".post-entry").find(".post-header__date-posted");
        let new_content = $(this).closest(".post-content").find(".js--post-edit__textarea").val();

        $(this).closest(".post-content").find(".js--post-body").text(new_content).show();

        //update post in the database using AJAX
        let post_id = $(this).closest(".post-entry").attr("data-pid");
        let user_id = $(this ).closest(".post-entry").attr("data-uid");
        
        //update the posts using AJAX
        $.post("inc/ajax/posts-ajax.php",{user_id:user_id, post_id:post_id, new_content:new_content, operation:'edit'})
            .done(function(result){
                
                if ( result != "" ) {
                    $(time_label).text("edited just now");

                }
            });
            $(this).closest(".post-content").find(".js--post-edit__container").remove();
    }); 
    

    //post-header__options-btn EventListener - hides the options menu on mouseleave
    $(document).on("mouseleave", ".js--post-header__options-menu", function(event){
        event.preventDefault();
        $(this).hide();
    });


    //Make the textarea of the Post Edit auto expand vertically
    $(document)
        .on('focus.js--auto-expand', 'textarea.js--auto-expand', function(){
            var savedValue = this.value;
            this.value = '';
            this.baseScrollHeight = this.scrollHeight;
            this.value = savedValue;
        })
        .on('input.js--auto-expand', 'textarea.js--auto-expand', function(){
            var minRows = this.getAttribute('data-min-rows')|0, rows;
            this.rows = minRows;
            rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 16);
            this.rows = minRows + rows;
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
                $.post("inc/ajax/fetch-comment-ajax.php",{post_id:post_id,the_comment:the_comment, operation:'insert'})
                    .done(function(result){
                        if ( result == "" ) {
                            // console.log("No Comment");
                        }
                        else {
                            $(result).insertAfter($(this_input).closest(".post-entry").find("[class*='js--pc']").last());
                            
                            // $(result).insertBefore($(this_input).closest(".post-comment__form"));
                            //clear the input textbox
                            $(this_input).text("");
                            $(this_input).blur();
                        }

                    });
                    
            }
            
        }
    });

    $(document).on("click", ".js--more_comments", function(event) {
        let more_comments_btn = event.target;
        let post_id = $(this).closest(".post-entry").attr("data-pid");
        let the_next_start = $(event.target).attr('data-start');

        let limit = 4;
        $.post("inc/ajax/fetch-comment-ajax.php",{post_id:post_id,start:the_next_start, limit:limit, operation:'fetch'})
            .done(function(result){
                if ( result == "" ) {
                    $(more_comments_btn).remove();
                }
                else {
                    $(result).insertBefore($(more_comments_btn));
                    $(more_comments_btn).remove();
                }

            });
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
        let targetForm = `.js--comfrm${post_id}`;
        targetForm = targetForm.replace("==","");

        //toggle the comment_form and comment posts of the selected post
        if ( $(targetForm).css("display") == "none" ) {
            $(targetForm).css("display","flex");
            // $(targetForm).closest(".post-entry").find(".post-comment__entry").css("display", "flex");
            
            //set the cursor inside the textbox
            $(targetForm).find(".js--post-comment__input").focus();
            $(targetForm).closest(".post-entry").find("[class*='js--pc']").show();
            
        }
        else {
            $(targetForm).css("display","none");
            $(targetForm).closest(".post-entry").find("[class*='js--pc']").hide();
            // $(targetForm).closest(".post-comment__entry").css("display", "none");

        }
        
    });

    //POSTS////////////////////////////////////////////
    let start = 0;
    let limit = 7;
    let ready_to_fetch = true;

    //function that sends ajax request to fetch-posts-ajax.php to pull posts from the database
    function loadUserPosts() {
        $.post("inc/ajax/fetch-posts-ajax.php",{start:start, limit:limit})
            .done(function(result){
                if ( result == "" ) {
                    ready_to_fetch = false;
                    $(".loading__info").hide();
                }
                else {
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

    $.post("inc/ajax/posts-ajax.php",{user_id:user_id, post_body:post_body, operation:"insert"})
    .done(function(result){
        //check if a post-entry exists; append to it if a post entry exists; otherwise insert before the loading_info div
        if ( document.querySelector(".post-entry") != null ) {
            $(result).insertBefore($(".post-entry").first(".post-comment__form"));
            // $(result).insertAfter($(".wall__posts"));
            // $(".wall__posts").append(result);
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





