<script>
/**
 * Portfolio Filter Frontend Scripts
 */
jQuery(document).ready(function ($) {
    const customSelects = document.querySelectorAll('.filter-select');
    let currentRequest = null;
    let currentPage = 1;
    let isLoading = false;
    let hasMorePosts = true;

    /**
     * Update URL parameters based on filter selections
     */
    function updateURLParams() {
        const params = new URLSearchParams(window.location.search);
        customSelects.forEach(select => {
            const name = select.getAttribute('name');
            const value = select.value;
            if (value) {
                params.set(name, value);
            } else {
                params.delete(name);
            }
        });
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        history.pushState({}, '', newUrl);
    }

    /**
     * Get filter parameters from URL
     * @returns {Object} Object containing filter values
     */
    function getFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        
        return {
            type: params.get("type") || '',
            topic: params.get("topic") || '',
            search: params.get("search") || '',
            sort: params.get("sort") || 'alphabetical',
        };
    }

    /**
     * Set filter dropdowns based on URL parameters
     */
    function setFiltersFromURL() {
        const filters = getFiltersFromURL();
        customSelects.forEach(select => {
            const name = select.getAttribute('name');
            if (filters[name]) {
                select.value = filters[name];
            }
        });
    }

    /**
     * Load portfolio items based on current filters
     * @param {boolean} isLoadMore Whether this is a "load more" request
     */
    function loadPortfolios(isLoadMore = false) {
        if (isLoading) return;

        // Reset to page 1 if not loading more
        if (!isLoadMore) {
            currentPage = 1;
            hasMorePosts = true;
        }

        const $grid = $('.resource-grid');
        const $loadMoreBtn = $('#load-more');
        const $btnLoader = $loadMoreBtn.find('.btn-blue-loader');
        const $btnArrow = $loadMoreBtn.find('.btn-blue-arrow');

        // Prepare filter data
        const filterData = {
            action: 'portfolio_filter',
            nonce: loadmore_params.nonce,
            type: $('#type').val(),
            topic: $('#topic').val(),
            search: $('#search').val(),
            sort: $('#sort').val(),
            page: currentPage
        };

        // Show loading indicators
        if (isLoadMore) {
            $btnLoader.show();
            $grid.css('opacity', '0.6');
            $btnArrow.hide();
        } else {
            $grid.css('opacity', '0.6');
        }

        // Abort any existing request
        if (currentRequest !== null) {
            currentRequest.abort();
        }

        // Execute AJAX request
        isLoading = true;
        currentRequest = $.ajax({
            url: loadmore_params.ajax_url,
            type: 'POST',
            data: filterData,
            dataType: 'json',
            success: function (response) {
                console.log(filterData)
                // Update UI with results
                if (isLoadMore) {
                    $grid.append(response.html);
                } else {
                    $grid.html(response.html);
                }

                // Update pagination state
                hasMorePosts = currentPage < response.max_pages;
                $loadMoreBtn.toggle(hasMorePosts);
            },
            error: function (xhr, status, error) {
                // Only retry if it's not an abort
                if (status !== 'abort') {
                    console.error('Error loading portfolios:', error);
                }
            },
            complete: function () {
                // Reset loading state
                $grid.css('opacity', '1');
                $btnLoader.hide();
                $btnArrow.show();
                currentRequest = null;
                isLoading = false;
            }
        });
    }

    // Load more button click handler
    $('#load-more').on('click', function () {
        if (!isLoading && hasMorePosts) {
            currentPage++;
            loadPortfolios(true);
        }
    });

    // Debounced filter change handler
    let filterTimeout;
    function applyFilters() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            updateURLParams();
            loadPortfolios(false);
        }, 300);
    }

    // Attach change event to all filter dropdowns
    customSelects.forEach(select => {
        select.addEventListener('change', applyFilters);
    });

    // Initialize filters from URL parameters
    setFiltersFromURL();
    
    // Load initial results
    loadPortfolios(false);
    
    // Handle browser back/forward navigation
    window.addEventListener('popstate', function() {
        setFiltersFromURL();
        loadPortfolios(false);
    });
});


/*** Tooltips javascript */
document.addEventListener("DOMContentLoaded", function () {
    const tooltips = document.querySelectorAll(".tooltip-container");

    tooltips.forEach((tooltip) => {
        tooltip.addEventListener("click", function () {
            // Hide all tooltips
            tooltips.forEach((t) => t.classList.remove("active"));

            // Toggle current tooltip
            this.classList.toggle("active");

            // Close tooltip when clicking outside
            document.addEventListener("click", function closeTooltip(event) {
                if (!tooltip.contains(event.target)) {
                    tooltip.classList.remove("active");
                    document.removeEventListener("click", closeTooltip);
                }
            });
        });
    });
});

</script>