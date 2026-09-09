document.addEventListener("DOMContentLoaded", function () {

    const toggle = document.getElementById("headerCollapse");
    const wrapper = document.getElementById("main-wrapper");

    if (toggle && wrapper) {
        toggle.addEventListener("click", function () {
            wrapper.classList.toggle("show-sidebar");
        });
    }

});