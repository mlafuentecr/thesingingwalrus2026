jQuery(document).ready(function($) {
    // Set an interval to trigger the click event every 30 seconds (30000 milliseconds)
    setInterval(function() {
        var cacheButton = $('#ess-grid-delete-cache');
        if (cacheButton.length) {
            console.log('Clicking the cache button');
            cacheButton.trigger('click');
        } else {
            console.log('Cache button not found');
        }
    }, 5000);
});