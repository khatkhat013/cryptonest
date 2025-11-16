/**
 * User Management Module
 */
const UserManagement = {
    init() {
        this.setupSearch();
        this.setupFilters();
        this.setupAssignAdmin();
    },

    /**
     * Set up user search functionality
     */
    setupSearch() {
        const searchInput = document.getElementById('userSearch');
        if (!searchInput) return;

        let debounceTimer;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.filterUsers(e.target.value.toLowerCase());
            }, 300);
        });
    },

    /**
     * Filter users table based on search term
     */
    filterUsers(searchTerm) {
        const tableBody = document.querySelector('table tbody');
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr:not(.no-results)');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const shouldShow = text.includes(searchTerm);
            row.style.display = shouldShow ? '' : 'none';
            if (shouldShow) visibleCount++;
        });

        // Handle no results message
        this.toggleNoResultsMessage(tableBody, visibleCount === 0, searchTerm);
    },

    /**
     * Toggle the no results message in the table
     */
    toggleNoResultsMessage(tableBody, show, searchTerm) {
        let noResults = tableBody.querySelector('.no-results');

        if (show) {
            if (!noResults) {
                noResults = document.createElement('tr');
                noResults.className = 'no-results';
                
                // Create elements safely without innerHTML to prevent XSS
                const td = document.createElement('td');
                td.colSpan = 8;
                td.className = 'text-center py-4';
                
                const icon = document.createElement('i');
                icon.className = 'bi bi-search fs-1 text-muted d-block';
                
                const p = document.createElement('p');
                p.className = 'mt-2';
                p.textContent = `No users found matching "${searchTerm}"`;
                
                td.appendChild(icon);
                td.appendChild(p);
                noResults.appendChild(td);
                tableBody.appendChild(noResults);
            }
        } else if (noResults) {
            noResults.remove();
        }
    },

    /**
     * Set up filter functionality
     */
    setupFilters() {
        const applyFiltersBtn = document.getElementById('applyFilters');
        const filterForm = document.getElementById('filterForm');
        
        if (!applyFiltersBtn || !filterForm) return;

        applyFiltersBtn.addEventListener('click', () => {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            
            // Add current search query if any
            const searchQuery = document.getElementById('userSearch')?.value;
            if (searchQuery) {
                params.append('search', searchQuery);
            }
            
            // Redirect with filters
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        });
    },

    /**
     * Set up admin assignment functionality
     */
    setupAssignAdmin() {
        const assignAdminModal = document.getElementById('assignAdminModal');
        if (!assignAdminModal) return;

        assignAdminModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            
            const form = assignAdminModal.querySelector('#assignAdminForm');
            const userNameElement = assignAdminModal.querySelector('#selectedUserName');
            
            if (form && userNameElement) {
                // Ensure the form action becomes /admin/users/{id}/assign
                // Support a few existing patterns: 
                // - /admin/users/0
                // - /admin/users/0/assign
                // - any trailing digits
                form.action = form.action.replace(/\/\d+(?:\/assign)?$/, `/${userId}/assign`);
                userNameElement.textContent = userName;
            }
        });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    UserManagement.init();
});