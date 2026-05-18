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

        if (hamburger && sidebar && overlay) {
            hamburger.addEventListener('click', function() {
                toggleSidebar();
            });

            overlay.addEventListener('click', function() {
                closeSidebar();
            });

            document.querySelectorAll('.sidebar-nav-item, .sidebar-nav-link, #sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    closeSidebar();
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
