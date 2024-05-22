button_profil_nav = document.querySelector(".profil");
console.log(button_profil_nav)
button_up_down = document.querySelector(".nav_up_down");
display_button_up_down = button_up_down.querySelectorAll("i");

console.log(display_button_up_down)

container_profil_nav = document.querySelector(".container_profil");
console.log(container_profil_nav)
function click_nav(){
    container_profil_nav.classList.toggle('nav_open');
    for (let index = 0; index < display_button_up_down.length; index++) {
        display_button_up_down[index].classList.toggle('nav_open');
    }
}

button_profil_nav.addEventListener("click", click_nav);
button_up_down.addEventListener("click", click_nav);