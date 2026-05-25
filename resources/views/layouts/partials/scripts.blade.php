<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay') || document.getElementById('sidebarOverlay');

        const closeSidebar = () => {
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
        };

        const openSidebar = () => {
            if (sidebar) sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
        };

        window.toggleSidebar = function() {
            if (sidebar && overlay) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
        };

        window.closeSidebar = closeSidebar;
        window.openSidebar = openSidebar;

        // Setup hamburger click handler
        if (hamburger) {
            hamburger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                window.toggleSidebar();
            });
        }

        // Setup overlay click handler
        if (overlay) {
            overlay.addEventListener('click', function() {
                closeSidebar();
            });
        }

        // Setup navigation links close on click
        if (sidebar) {
            const navLinks = document.querySelectorAll('.sidebar-nav-item, .sidebar-nav-link, #sidebar a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't close if it's the logout form submit
                    if (!link.querySelector('form')) {
                        closeSidebar();
                    }
                });
            });
        }

        const scrollButton = document.getElementById('scrollToTop');
        if (scrollButton) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollButton.classList.add('show');
                } else {
                    scrollButton.classList.remove('show');
                }
            });

            scrollButton.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
</script>
