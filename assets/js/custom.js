jQuery(document).ready(function($) {
	$(".esg-media-cover-wrapper").click(function() {
		window.location = $(this).find("a").first().attr("href");
		return false;
	});
});


