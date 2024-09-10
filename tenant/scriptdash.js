document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const homeSection = document.querySelector('.home-section');
    const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn'); // Ensure this selector matches your actual toggle button
    const sidebarLinks = document.querySelectorAll('.nav-links a');
    const contentArea = document.getElementById('content-area');
    const pageTitle = document.querySelector('.dashboard');

    // Toggle sidebar visibility
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        homeSection.classList.toggle('active');
    }

    // Event listener for the sidebar toggle button
    sidebarToggleBtn.addEventListener('click', toggleSidebar);

    // Optional: Close sidebar when clicking outside of it
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !sidebarToggleBtn.contains(event.target)) {
            if (sidebar.classList.contains('active')) {
                toggleSidebar();
            }
        }
    });

    // Handle resizing to adjust layout
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 400) {
            sidebar.classList.add('active');
            homeSection.classList.add('active');
        } else if (window.innerWidth <= 1240) {
            sidebar.classList.remove('active');
            homeSection.classList.remove('active');
        }
    });

    // Initial load of the default page
    loadPage('dashboard');

    // Add event listeners to sidebar links
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Update the active link
            sidebarLinks.forEach(link => link.classList.remove('active'));
            this.classList.add('active');

            // Update the page title
            pageTitle.textContent = this.querySelector('.links_name').textContent;

            // Load the content via AJAX
            const page = this.getAttribute('data-page');
            loadPage(page);
        });
    });

    // Function to load content via AJAX
    function loadPage(page) {
        fetch(`${page}.php`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                contentArea.innerHTML = data;
            })
            .catch(error => console.error('Error loading content:', error));
    }
});
