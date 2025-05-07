</div> <!-- End of row from header -->
    </div> <!-- End of container-fluid from header -->
</div> <!-- End of content-wrapper from header -->

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo '/School System Management CS/assets/js/script.js'; ?>"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced sidebar toggle functionality
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn');
            const sidebarCloseBtn = document.querySelector('.sidebar-toggle');
            const sidebarCollapseBtn = document.querySelector('.sidebar-collapse');
            const mainContent = document.querySelector('.main-content');
            
            // Toggle sidebar function for mobile
            function toggleSidebar() {
                sidebar.classList.toggle('expanded');
                mainContent.classList.toggle('expanded');
                
                // On mobile, also toggle the sidebar overlay
                if (window.innerWidth < 768) {
                    if (sidebar.classList.contains('expanded')) {
                        sidebarOverlay.style.display = 'block';
                        setTimeout(() => {
                            sidebarOverlay.style.opacity = '1';
                        }, 10);
                        document.body.style.overflow = 'hidden'; // Prevent scrolling
                    } else {
                        sidebarOverlay.style.opacity = '0';
                        setTimeout(() => {
                            sidebarOverlay.style.display = 'none';
                        }, 300); // Match transition duration
                        document.body.style.overflow = ''; // Allow scrolling
                    }
                }
            }
            
            // Collapse sidebar function for desktop (minimizes it)
            function collapseSidebar() {
                // This fixes the main issue - we need to toggle "collapsed" class, not "expanded"
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Toggle chevron direction
                const chevron = sidebarCollapseBtn.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    chevron.classList.remove('bx-chevron-left');
                    chevron.classList.add('bx-chevron-right');
                } else {
                    chevron.classList.remove('bx-chevron-right');
                    chevron.classList.add('bx-chevron-left');
                }
                
                // Store preference in local storage
                localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
            }
            
            // Add click event listeners
            if (sidebarToggleBtn) {
                sidebarToggleBtn.addEventListener('click', toggleSidebar);
            }
            
            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', toggleSidebar);
            }
            
            if (sidebarCollapseBtn) {
                sidebarCollapseBtn.addEventListener('click', collapseSidebar);
                sidebarCollapseBtn.style.cursor = 'pointer';
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }
            
            // Restore sidebar state from local storage
            const savedSidebarState = localStorage.getItem('sidebarState');
            if (savedSidebarState === 'collapsed' && window.innerWidth >= 768) {
                // Only apply saved state on desktop
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                
                // Update chevron icon
                const chevron = sidebarCollapseBtn?.querySelector('i');
                if (chevron) {
                    chevron.classList.remove('bx-chevron-left');
                    chevron.classList.add('bx-chevron-right');
                }
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    // On desktop/tablet, hide overlay
                    sidebarOverlay.style.opacity = '0';
                    sidebarOverlay.style.display = 'none';
                    document.body.style.overflow = '';
                } else {
                    // On mobile, reset sidebar state and close it
                    sidebar.classList.remove('collapsed');
                    if (!sidebar.classList.contains('expanded')) {
                        mainContent.classList.add('expanded');
                    }
                }
            });
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Add animation classes to elements
            document.querySelectorAll('.card').forEach((card, index) => {
                card.classList.add('fade-in');
                card.style.animationDelay = `${index * 0.1}s`;
            });
            
            // Table row animations
            document.querySelectorAll('tbody tr').forEach((row, index) => {
                row.classList.add('slide-in');
                row.style.animationDelay = `${index * 0.05}s`;
            });
            
            // Fix chart responsiveness
            const chartCanvases = document.querySelectorAll('canvas');
            chartCanvases.forEach(canvas => {
                // Add chart class for better responsive styling
                canvas.classList.add('chart');
            });
            
            // Smart Print functionality
            const printButtons = document.querySelectorAll('.print-btn');
            
            printButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // If it's not a direct print button (with href)
                    if (!this.getAttribute('href') || this.getAttribute('href').indexOf('print=') === -1) {
                        e.preventDefault();
                        
                        const printTarget = this.getAttribute('data-print-target');
                        
                        if (printTarget) {
                            // Individual student print
                            const targetElement = document.getElementById(printTarget);
                            
                            if (targetElement) {
                                // Mark this section as active for printing
                                document.body.classList.add('print-selected');
                                
                                // Remove any previously active print sections
                                document.querySelectorAll('.print-section').forEach(section => {
                                    section.classList.remove('print-active');
                                });
                                
                                // Add active class to current section
                                targetElement.classList.add('print-active');
                                
                                // Trigger print
                                setTimeout(() => {
                                    window.print();
                                    
                                    // Clean up after print dialog closes
                                    setTimeout(() => {
                                        document.body.classList.remove('print-selected');
                                        targetElement.classList.remove('print-active');
                                    }, 500);
                                }, 200);
                            }
                        } else {
                            // Print all or current view
                            window.print();
                        }
                    }
                });
            });
            
            // Handle keyboard navigation for accessibility
            document.addEventListener('keydown', function(e) {
                // ESC key closes sidebar on mobile
                if (e.key === 'Escape' && window.innerWidth < 768 && sidebar.classList.contains('expanded')) {
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>