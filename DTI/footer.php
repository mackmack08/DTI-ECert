    <!-- Bootstrap JS for functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($additionalScripts)) echo $additionalScripts; ?>
    
    <script>
        // Toggle the sidebar when the hamburger icon is clicked
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            
            // Update header margin when sidebar state changes
            const header = document.querySelector('.header');
            const sidebar = document.querySelector('.sidebar');
            const sidebarHeader = document.querySelector('.sidebar-header');
            const miniRectangle = document.querySelector('.mini-rectangle');
            
            if (sidebar.classList.contains('collapsed')) {
                header.style.marginLeft = 'var(--sidebar-collapsed-width)';
                sidebarHeader.style.width = 'var(--sidebar-collapsed-width)';
                miniRectangle.style.width = 'var(--sidebar-collapsed-width)';
            } else {
                header.style.marginLeft = 'var(--sidebar-width)';
                sidebarHeader.style.width = 'var(--sidebar-width)';
                miniRectangle.style.width = 'var(--sidebar-width)';
            }
        });

        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            // Start with expanded sidebar on larger screens, collapsed on mobile
            const sidebar = document.querySelector('.sidebar');
            const header = document.querySelector('.header');
            const sidebarHeader = document.querySelector('.sidebar-header');
            const miniRectangle = document.querySelector('.mini-rectangle');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                header.style.marginLeft = 'var(--sidebar-collapsed-width)';
                sidebarHeader.style.width = 'var(--sidebar-collapsed-width)';
                miniRectangle.style.width = 'var(--sidebar-collapsed-width)';
            } else {
                header.style.marginLeft = 'var(--sidebar-width)';
                sidebarHeader.style.width = 'var(--sidebar-width)';
                miniRectangle.style.width = 'var(--sidebar-width)';
            }
            
            // Handle window resize to adjust fixed elements
            window.addEventListener('resize', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    header.style.marginLeft = 'var(--sidebar-collapsed-width)';
                    sidebarHeader.style.width = 'var(--sidebar-collapsed-width)';
                    miniRectangle.style.width = 'var(--sidebar-collapsed-width)';
                } else {
                    if (!sidebar.classList.contains('collapsed')) {
                        header.style.marginLeft = 'var(--sidebar-width)';
                        sidebarHeader.style.width = 'var(--sidebar-width)';
                        miniRectangle.style.width = 'var(--sidebar-width)';
                    }
                }
            });
            
            // Add click event listeners to all navigation links
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Remove active class from all links
                    navLinks.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Update breadcrumb
                    updateBreadcrumb(this.getAttribute('data-path'));
                });
            });
        });

        // Function to update the breadcrumb
        function updateBreadcrumb(path) {
            const breadcrumb = document.querySelector('.breadcrumb');
            
            // Clear existing items except Home
            while (breadcrumb.children.length > 1) {
                breadcrumb.removeChild(breadcrumb.lastChild);
            }
            
            // Add the new path
            const li = document.createElement('li');
            li.className = 'breadcrumb-item active';
            li.setAttribute('aria-current', 'page');
            li.textContent = path;
            breadcrumb.appendChild(li);
            
            // If path contains subpaths (e.g., "Client Management/Add Client")
            if (path.includes('/')) {
                const parts = path.split('/');
                
                // Clear again
                while (breadcrumb.children.length > 1) {
                    breadcrumb.removeChild(breadcrumb.lastChild);
                }
                
                // Add each part
                parts.forEach((part, index) => {
                    const li = document.createElement('li');
                    li.className = 'breadcrumb-item';
                    
                    if (index === parts.length - 1) {
                        li.classList.add('active');
                        li.setAttribute('aria-current', 'page');
                        li.textContent = part.trim();
                    } else {
                        const a = document.createElement('a');
                        a.href = '#';
                        a.textContent = part.trim();
                        li.appendChild(a);
                    }
                    
                    breadcrumb.appendChild(li);
                });
            }
        }
    </script>
</body>
</html>
