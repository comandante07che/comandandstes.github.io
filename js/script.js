var link = document.querySelector(".user_block");

var popup = document.querySelector(".modal_content");
var overlay = document.querySelector(".modal_overlay");
var close = popup.querySelector(".modal_content_close");

var form = popup.querySelector("form");
var login = popup.querySelector("[name=login]");
var password = popup.querySelector("[name=password]");

var storage = localStorage.getItem("login");

var mapOpen = document.querySelector(".js_open_map");

var mapPopup = document.querySelector(".modal_content_map");
var mapClose = mapPopup.querySelector(".modal_content_close");

link.addEventListener("click", function(event) {
    event.preventDefault();
    popup.classList.add("modal_content_show");
    overlay.classList.add("modal_overlay_show");

    if (storage) {
        login.value = storage;
        password.focus();
    } else {
        login.focus();
        }

    });

close.addEventListener("click", function(event) {
    event.preventDefault();
    popup.classList.remove("modal_content_show");
    popup.classList.remove("modal_error");
    overlay.classList.remove("modal_overlay_show");
});

form.addEventListener("submit", function(event) {
    if (!login.value || !password.value) {
        event.preventDefault();
        popup.classList.remove("modal_error");
        popup.offsetWidth = popup.offsetWidth;
        popup.classList.add("modal_error");
    } else {
        localStorage.setItem("login", login.value);
        }
});

window.addEventListener("keydown", function(event) {
    if (event.keyCode === 27) {
        if (popup.classList.contains("modal_content_show")) {
        popup.classList.remove("modal_content_show");
        popup.classList.remove("modal_error");
        overlay.classList.remove("modal_overlay_show");
        }
    }
});

mapOpen.addEventListener("click", function(event) {
    event.preventDefault();
    mapPopup.classList.add("modal_overlay_show");
    overlay.classList.add("modal_overlay_show");
});

mapClose.addEventListener("click", function(event) {
    event.preventDefault();
    mapPopup.classList.remove("modal_overlay_show");
    overlay.classList.remove("modal_overlay_show");
});

window.addEventListener("keydown", function(event) {
    if (event.keyCode === 27) {
        if (mapPopup.classList.contains("modal_overlay_show")) {
            mapPopup.classList.remove("modal_overlay_show");
            overlay.classList.remove("modal_overlay_show");
        }
    }
});
