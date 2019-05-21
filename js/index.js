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


	$('.owl-carousel').owlCarousel({
		autoplay:true,
		autoplayHoverPause:true,
		loop:true,
        lazyLoad:true,
        nav:true,
        dots:true,
        responsive: {
        	0: {
        		items: 1,
        		dots: true
        	},
        	485: {
        		items: 2,
        		dots: true
        	},
        	728: {
        		items: 3,
        		dots: true
        	},
        	960: {
        		items: 4,
        		dots: true
        	}
        }
    });

    $('.owl-carousel').on('mousewheel', '.owl-stage', function (e) {
    	if(e.deltaY > 0){
    		$('.owl-carousel').trigger('prev.owl');
    	} else{
    		$('.owl-carousel').trigger('next.owl');
    	}
    	e.preventDefault();
    });


})(window.jQuery);