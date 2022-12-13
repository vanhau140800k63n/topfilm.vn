<header>
	<div class="box">
		<div class="logo glow"><a href="{{route('home')}}">TOPFILM</a></div>
		<div class="search">
			<form action="{{route('key-search')}}" method="post">
				@csrf
				<input type="text" name="keyword" placeholder="Tên phim ..." class="search__input">
				<button class="search__btn">Tìm kiếm</button>
			</form>
		</div>
		<?php
		$search_advanced_list = \App\Constants\AdvancedSearch::SEARCH_LIST;
		?>
		<div class="advanced_search">
			@foreach($search_advanced_list as $as_key => $as_container)
			<div class="as_name" id_key="{{$as_key}}">{{$as_container['name']}} <i class="fa-solid fa-caret-down"></i>
			</div>

			<div class="as_container" id="as_container{{$as_key}}">
				@foreach($as_container['screeningItems'] as $key_screening_items => $screening_items)
				<div class="as_items">
					<div class="as_items_name"> {{ __('search_advanced.'. $screening_items['name'])}}</div>
					@foreach($screening_items['items'] as $key_as_items=> $as_item)
					<?php
					$active = '';
					if (isset($value)) {
						if (isset($index['a']) && (!empty($index['a']) || intval($index['a']) === 0) && intval($index['a']) === intval($as_key)) {
							$valueUrl = $value;
							if (isset($index['b']) && (!empty($index['b']) || intval($index['b']) === 0) && intval($index['b']) === intval($key_as_items) && $key_screening_items === 0) {
								$active = 'active';
							}
							if (isset($index['c']) && (!empty($index['c']) || intval($index['c']) === 0) && intval($index['c']) === intval($key_as_items) && $key_screening_items === 1) {
								$active = 'active';
							}
							if (isset($index['d']) && (!empty($index['d']) || intval($index['d']) === 0) && intval($index['d']) === intval($key_as_items) && $key_screening_items === 2) {
								$active = 'active';
							}
						} else {
							$valueUrl = 'a' . $as_key . 'bcd';
						}
					} else {
						$valueUrl = 'a' . $as_key . 'bcd';
					}
					$posB = strpos($valueUrl, 'b');
					$posC = strpos($valueUrl, 'c');
					$posD = strpos($valueUrl, 'd');

					$valueBUrl = substr($valueUrl, $posB, $posC - $posB);
					$valueCUrl = substr($valueUrl, $posC, $posD - $posC);
					$valueDUrl = substr($valueUrl, $posD);

					if ($key_screening_items === 0) {
						$url = str_replace($valueBUrl, 'b' . $key_as_items, $valueUrl);
					} elseif ($key_screening_items === 1) {
						$url = str_replace($valueCUrl, 'c' . $key_as_items, $valueUrl);
					} else {
						$url = str_replace($valueDUrl, 'd' . $key_as_items, $valueUrl);
					}

					$url = route('search_advanced', $url);
					?>
					<a class="as_item {{ $active }}" id="s{{$as_key}}" href="{{ $url }}">
						@if (trans()->has('search_advanced.detail.' . $as_item['name']))
						{{ __('search_advanced.detail.'. $as_item['name'])}}
						@else
						{{ $as_item['name'] }}
						@endif
					</a>
					@endforeach
				</div>
				@endforeach
				<div class="close_search_advanced">
					<button class="close_search_advanced_btn">Đóng</button>
				</div>
			</div>
			@endforeach
		</div>
	</div>
	<div id="preloader">
		<div id="loader"></div>
	</div>
</header>
<script>
	$('.as_name').hover(function() {
		$('.as_container').each(function() {
			$(this).hide();
		})
		$('.as_name').each(function() {
			$(this).css('color', '#fff');
			$(this).css('background', 'none');
		})
		$('#as_container' + $(this).attr('id_key')).show();
		$(this).css('color', '#000');
		$(this).css('background', '#fff');
	}, function() {
		if ($('#as_container' + $(this).attr('id_key') + ':hover').length == 0) {
			$('#as_container' + $(this).attr('id_key')).hide();
			$(this).css('color', '#fff');
			$(this).css('background', 'none');
		}
	})
	$('.as_container').mouseout(function() {
		if ($('.as_container div:hover').length == 0) {
			$(this).hide();
			$('.as_name').each(function() {
				$(this).css('color', '#fff');
				$(this).css('background', 'none');
			})
		}
	})

	$('.close_search_advanced').click(function() {
		$(this).parent().hide();
		$('.as_name').each(function() {
			$(this).css('color', '#fff');
			$(this).css('background', 'none');
		})
	})
</script>