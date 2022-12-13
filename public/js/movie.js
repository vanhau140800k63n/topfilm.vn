var video = document.getElementById('video_media');

document.onkeydown = function(event) {
	switch (event.keyCode) {
		case 37:
		event.preventDefault();

		vid_currentTime = video.currentTime;
		video.currentTime = vid_currentTime - 5;
		break;

		case 39:
		event.preventDefault();

		vid_currentTime = video.currentTime;
		video.currentTime = vid_currentTime + 5;
		break;
	}
};

function restart() {
	if(video.readyState == 0) {
		let episode_id = Number($('#media').attr('id_episode'));
		let definition = $('.movie__quality').children(":selected").attr("id");
		reload(episode_id, definition);
	} else {
        if(video.textTracks.length == 1) {
        	video.textTracks[0].mode = 'hidden';
        }
        if(video.textTracks.length == 1) {
        	video.textTracks[0].mode = 'showing';
        }
		clearInterval(restart_media);
	}
}

 
$('.movie__similar img').css('max-height', $('.movie__similar img').width()*1.4);
$('.movie__media').height($('.movie__media').width() * 1080 / 1920);

$('.episode').each(function() {
	if($(this).attr('id') == $('#media').attr('id_episode')) {
		$(this).css('background-color', '#ed5829');
	}
})


$('.movie__play').click(function() {
	$('.movie__play').css('display', 'none');

	load();
})

function load() {
    $('.movie__load').css('display','block');

	let _token = $('input[name="_token"]').val();
	$.ajax({
		url: 'episode-ajax',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		type: "POST",
		dataType: 'json',
		data: {
			id: $('#media').attr('id_media'),
			category: $('#media').attr('category'),
			episode_id: $('#media').attr('id_episode'),
			definition: null,
			_token: _token
		}
	}).done(function (data) {
		$('#media').val(data['mediaUrl']);
		window.history.pushState({}, '', data[2]);

		((source) => {
			if (typeof Hls == "undefined") return console.error("HLS Not Found");
			if (!document.querySelector("video")) return;
			var hls = new Hls();
			hls.loadSource(source);
			hls.attachMedia(document.querySelector("video"));
		})(data['mediaUrl']);

		// let movie__quality = '';
		// for(let i = 0; i < data['0'].length; ++i) {
		// 	movie__quality += '<option id="'+ data['0'][i]['code'] +'">'+ data['0'][i]['description'] +'</option>';
		// }
		// $('.movie__quality').html(movie__quality);

        let subtitle = '';
        for(let i = 0; i < data['1'].length; ++i) {
        	if(data['1'][i]['languageAbbr'] == 'vi') {
        		subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] +'" srclang="' + data['1'][i]['languageAbbr'] +'" src="https://srt-to-vtt.vercel.app/?url='+ data['1'][i]['subtitlingUrl'] +'" >';
        	}
        }
        $('.movie__screen').html(subtitle);
        $('.movie__name').html($('.movie__name').attr('id') + data[3]);
        $('.movie__load').css('display','none');

        restart_media = setInterval(restart, 2000);

		return true;
	}).fail(function (e) {
		return false;
	});
}

function reload(episode_id, definition) {
	
	let _token = $('input[name="_token"]').val();
	$.ajax({
		url: 'episode-ajax',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		type: "POST",
		dataType: 'json',
		data: {
			id: $('#media').attr('id_media'),
			category: $('#media').attr('category'),
			episode_id: episode_id,
			definition: definition,
			_token: _token
		}
	}).done(function (data) {
		var video = document.getElementById("video_media");
		if(video.readyState != 0) {
			return true;
		}
		$('#media').val(data['mediaUrl']);

		((source) => {
			if (typeof Hls == "undefined") return console.error("HLS Not Found");
			if (!document.querySelector("video")) return;
			var hls = new Hls();
			hls.loadSource(source);
			hls.attachMedia(document.querySelector("video"));
		})(data['mediaUrl']);

		let subtitle = '';
        for(let i = 0; i < data['1'].length; ++i) {
        	if(data['1'][i]['languageAbbr'] == 'vi') {
        		subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] +'" srclang="' + data['1'][i]['languageAbbr'] +'" src="https://srt-to-vtt.vercel.app/?url='+ data['1'][i]['subtitlingUrl'] +'" >';
        	}
        }
        $('.movie__screen').html(subtitle);

		return true;
	}).fail(function (e) {

		return false;
	});
}

function loadByDefinition(episode_id, definition) {
	
	let _token = $('input[name="_token"]').val();
	$.ajax({
		url: 'episode-ajax',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		type: "POST",
		dataType: 'json',
		data: {
			id: $('#media').attr('id_media'),
			category: $('#media').attr('category'),
			episode_id: episode_id,
			definition: definition,
			_token: _token
		}
	}).done(function (data) {
		$('#media').val(data['mediaUrl']);

		((source) => {
			if (typeof Hls == "undefined") return console.error("HLS Not Found");
			if (!document.querySelector("video")) return;
			var hls = new Hls();
			hls.loadSource(source);
			hls.attachMedia(document.querySelector("video"));
		})(data['mediaUrl']);

		let subtitle = '';
        for(let i = 0; i < data['1'].length; ++i) {
        	if(data['1'][i]['languageAbbr'] == 'vi') {
        		subtitle = '<track kind="subtitles" label="' + data['1'][i]['language'] +'" srclang="' + data['1'][i]['languageAbbr'] +'" src="https://srt-to-vtt.vercel.app/?url='+ data['1'][i]['subtitlingUrl'] +'" >';
        	}
        }
        $('.movie__screen').html(subtitle);

		restart_media = setInterval(restart, 2000);

		return true;
	}).fail(function (e) {

		return false;
	});
}


$('.episode').click(function() {
	$('.episode').each(function() {
		$(this).css('background-color', '#27282e');
	})
	$(this).css('background', '#ed5829');
	$('#media').attr('id_episode', $(this).attr('id'));


	load();
})

$('.movie__quality').change(function() {
	let episode_id = Number($('#media').attr('id_episode'));
	let definition = $(this).children(":selected").attr("id");

	loadByDefinition(episode_id, definition);
})
