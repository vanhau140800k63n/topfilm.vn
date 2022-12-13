@extends('layouts.master')
@section('meta')
<title>Tìm kiếm</title>
@endsection
@section('content')
<div class="box search_advanced_film">
	<div id="preloader">
		<div id="loader"></div>
	</div>
</div>
<script>
	let _token = $('input[name="_token"]').val();
	$.ajax({
		url: "{{ route('search_advanced_first') }}",
		type: "POST",
		dataType: 'json',
		data: {
			params:   "{{ $param['a'] }}",
			area:     "{{ $param['b'] }}",
			category: "{{ $param['c'] }}",
			year:     "{{ $param['d'] }}",
			_token:   _token
		}
	}).done(function(data) {
		$('.box.advanced').removeClass('homepage').addClass('search_advanced_film');
		$('.box.search_advanced_film').html(data[0]);
		if (data[1] < 18) {
			$('.lds-facebook').remove();
		}
		$('#preloader').hide();
		return true;
	}).fail(function(e) {
		$('.box.advanced').removeClass('homepage').addClass('search_advanced_film');
		$('.box.search_advanced_film').html('<div style="padding-top: 30px; font-weight: 600; font-size: 20px">Không tìm thấy phim</div>');
		$('#preloader').hide();
		return false;
	});

	var scroll = true;
	$(window).scroll(function() {
		if ($('.box').hasClass('search_advanced_film')) {
			value = $('header').height() + $(".search_advanced_film").height() - $(window).scrollTop() - $(window).height() - 1000;
			if (value < 0 && scroll && $('#info').attr('count') == 18) {
				scroll = false;
				let _token = $('input[name="_token"]').val();
				$.ajax({
					url: "{{ route('search_advanced_more') }}",
					type: "POST",
					dataType: 'json',
					data: {
						params: "{{ $param['a'] }}",
						area: "{{ $param['b'] }}",
						category: "{{ $param['c'] }}",
						year: "{{ $param['d'] }}",
						sort: $('#info').attr('sort'),
						_token: _token
					}
				}).done(function(data) {
					$('.recommend__item').html($('.recommend__item').html() + data[0]);
					$('#info').remove();
					$('.recommend__items').html($('.recommend__items').html() + data[1]);
					scroll = true;
					if (data[2] < 18) {
						$('.lds-facebook').remove();
					}
					return true;
				}).fail(function(e) {
					$('.lds-facebook').remove();
					scroll = true;
					$('#info').attr('count', 0);
					return false;
				});
			}
		}
	});
</script>
@endsection