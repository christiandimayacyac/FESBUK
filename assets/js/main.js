document.addEventListener('DOMContentLoaded', function() {
    let switches =  document.querySelectorAll(".js--switch");

    for(let i=0;i<switches.length; i++) {
        switches[i].addEventListener('click',toggleDisplay);
    }
});

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





