@extends('layouts.master')
@section('meta')
<title>Tìm kiếm: {{$key}}</title>
@endsection
@section('content')
<div class="box search_by_keyword">
	<div id="preloader">
		<div id="loader"></div>
	</div>
</div>
<script>
	$('#preloader').show();
	$.ajax({
		url: "{{ route('search_by_keyword_ajax', $key) }}",
		type: "GET",
		dataType: 'json',
	}).done(function(data) {
		$('.search_by_keyword').html(data);
		$('.image').css('max-height', $('.card__film').width() * 1.4);
		$('#preloader').hide();
		return true;
	}).fail(function(e) {
		return false;
	});
</script>
@endsection