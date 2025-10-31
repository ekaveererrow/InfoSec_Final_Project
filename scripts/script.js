document.addEventListener("DOMContentLoaded", function () {
    // Load Sidebar
    fetch("sidebar.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("sidebar-container").innerHTML = data;
        });

    // Load Header
    fetch("header.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("header-container").innerHTML = data;
        });
});

document.addEventListener("DOMContentLoaded", function () {
    const menuLinks = document.querySelectorAll(".sidebar a");

    // Get current page filename (ignoring query params)
    const currentPage = window.location.pathname.split("/").pop();

    // Loop through sidebar links and check if they match the current page
    menuLinks.forEach(link => {
        const linkPage = link.getAttribute("href").split("/").pop();
        if (linkPage === currentPage) {
            link.classList.add("active");
        } else {
            link.classList.remove("active");
        }
    });
});
