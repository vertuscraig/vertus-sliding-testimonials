jQuery(document).ready(function($) {

	var objheight = $(".slide").height();
	$('.slider').height(objheight+30);

	$(window).resize(function() {
    var objheight = $(".slide").height();
    $(".slider").height(objheight+30);
});
});