/**
 * public/assets/js/search.js
 * Handles product catalog search input, query validation, and navigation.
 */
document.addEventListener('DOMContentLoaded', function () {
    var searchForm = document.querySelector('.search-bar');
    var searchInput = document.querySelector('.search-bar input.q');

    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var query = searchInput.value.trim();
            if (query.length > 0) {
                // Navigate to categories with the search parameter
                window.location.href = 'categories.html?search=' + encodeURIComponent(query);
            } else {
                searchInput.focus();
            }
        });
    }
});
