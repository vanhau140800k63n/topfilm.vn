@extends('layouts.master')
@section('meta')
<title>TOPFILM - Xem phim FullHD Vietsub mới nhất</title>
@endsection
@section('content')
<button class="btn btn-primary"></button>
<div class="box homepage advanced" id="2">
	<div class="loader_home">
		<div class="inner one"></div>
		<div class="inner two"></div>
		<div class="inner three"></div>
	</div>
</div>
<script>
	$.ajax({
		url: "{{ route('load_first_home_ajax') }}",
		type: "GET",
		dataType: 'json',
	}).done(function(data) {
		$('.homepage.advanced').html(data);

		let swiper__slider_img_width = $('.swiper__slider img').width();
		let swiper__slider_img_height = $('.swiper__slider img').height();

		let position = (swiper__slider_img_width - swiper__slider_img_height / 2.5) / 2;
		$('.swiper__slider img').css('object-position', '0px -' + position + 'px');

		$('.loader_home').remove();

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

		$('.home__category').click(function() {
			$(this).attr('data');
		})
		return true;
	}).fail(function(e) {
		return false;
	});

	var scroll = true;
	$(window).scroll(function() {
		if ($('.box').hasClass('homepage')) {
			value = $('header').height() + $(".homepage").height() - $(window).scrollTop() - $(window).height() - 1000;
			if (value < 0 && scroll) {
				scroll = false;
				let _token = $('input[name="_token"]').val();
				$.ajax({
					url: "{{route('home-ajax')}}",
					type: "POST",
					dataType: 'json',
					data: {
						page: $('.homepage').attr('id'),
						width: $('.image').width(),
						_token: _token
					}
				}).done(function(data) {
					$('.lds-facebook').remove();
					$('.listfilm').html($('.listfilm').html() + data[0]);
					$('.homepage').attr('id', data[1]);
					scroll = true;

					return true;
				}).fail(function(e) {
					return false;
				});
			}
		}
	});
</script>
@endsection