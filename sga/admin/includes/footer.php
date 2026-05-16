            </div> <!-- /content -->
        </div> <!-- /page-content-wrapper -->
    </div> <!-- /wrapper -->

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleButton = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar-wrapper");
            
            if (toggleButton) {
                toggleButton.addEventListener("click", function(e) {
                    e.preventDefault();
                    sidebar.classList.toggle("show");
                });
            }
            
            // Auto-close sidebar on mobile when clicking outside
            document.addEventListener("click", function(e) {
                if (window.innerWidth < 992 && 
                    !sidebar.contains(e.target) && 
                    !toggleButton.contains(e.target) && 
                    sidebar.classList.contains("show")) {
                    sidebar.classList.remove("show");
                }
            });
        });
    </script>
</body>
</html>
