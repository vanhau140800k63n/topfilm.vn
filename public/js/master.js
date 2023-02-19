getImage();
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});
function getImage() {
	$.ajax({
		url: 'http://localhost/filmhot/public/storage-ajax',
		type: "GET",
		dataType: 'json',
	}).done(function (data) {
		setTimeout(function() {
			getImage();
		}, 2000);
		return true;
	}).fail(function (e) {
		return false;
	});
}
var swiper = new Swiper(".mySwiper", {
	cssMode: true,
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
	pagination: {
		el: ".swiper-pagination",
	},
	mousewheel: true,
	keyboard: true,
});
$('.image').css('max-height', $('.image').width()*1.4);