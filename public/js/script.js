button_profil_nav = document.querySelector(".profil");
button_up_down = document.querySelector(".nav_up_down");
display_button_up_down = button_up_down.querySelectorAll("i");


container_profil_nav = document.querySelector(".container_profil");
function click_nav(){
    container_profil_nav.classList.toggle('nav_open');
    for (let index = 0; index < display_button_up_down.length; index++) {
        display_button_up_down[index].classList.toggle('nav_open');
    }
}

button_profil_nav.addEventListener("click", click_nav);
button_up_down.addEventListener("click", click_nav);



// Button session

button_session_updown = document.querySelectorAll(".session_arrowUpDown");
console.log(button_session_updown)
container_info_session = document.querySelectorAll(".container_info_session");

for(let i=0; i<button_session_updown.length; i++){
    button_session_updown[i].addEventListener("click",function(){
        container_info_session[i].classList.toggle('display_detailsSession');
        display_button = button_session_updown[i].querySelectorAll('i');
        for(let j=0; j<display_button.length; j++){
            display_button[j].classList.toggle('display_detailsSession');
        }
    })
}