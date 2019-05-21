(function ($) {

	"use strick";

	// Closes responsive menu when a scroll trigger link is clicked
	$('.js-scroll-trigger').click(function() {
		$('.navbar-collapse').collapse('hide');
	});

	// Collapse Navbar
	var navbarCollapse = function() {
		var x = screen.availWidth;
		if(x > 992){
			if ($("#mainNav").offset().top > 100) {
				$("#mainNav").addClass("navbar-shrink");
			} else {
				$("#mainNav").removeClass("navbar-shrink");
			}
		}
	};
	// Collapse now if page is not at top
	navbarCollapse();
	// Collapse the navbar when page is scrolled
	$(window).scroll(navbarCollapse);


	// SWITCH DESC-REVIEW

	var toDesc = 'section#desc-review div#title p#to-desc';
	var toReview = 'section#desc-review div#title p#to-review';

	$(toDesc).click(function() {
		$('#desc').removeClass('d-none');
		$('#review').addClass('d-none');
	});
	$(toReview).click(function() {
		$('#desc').addClass('d-none');
		$('#review').removeClass('d-none');
	});

})(window.jQuery);