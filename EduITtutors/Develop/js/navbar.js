document.addEventListener('DOMContentLoaded', function () {
    // Auto-collapse on nav link click (mobile only)
    const navLinks = document.querySelectorAll('.navbar-collapse .nav-link, .navbar-collapse .dropdown-item');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    navLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        });
    });
});