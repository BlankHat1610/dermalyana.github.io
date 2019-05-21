jQuery(document).ready(function() {

	$(".sign-up").hide();

	$("#create").click(function(){
		$(".sign-in").fadeOut(1000);
		$(".sign-up").fadeIn(1000);
	});

	$("#signin").click(function(){
		$(".sign-up").fadeOut(1000);
		$(".sign-in").fadeIn(1000);
	});

	$("#forget").click(function() {
		window.alert("We have send your email! \nCheck it out!");
	});

	$("#sign-in-input").click(function() {
		window.open("../html/index.html", "_self");
	});

	$("#sign-up-input").click(function() {
		window.open("../html/index.html", "_self");
	});
});