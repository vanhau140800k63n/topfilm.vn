@extends('layouts.master')
@section('meta')
    <meta name="description"
        content="Xem phim {{ $movie_detail->name }} FullHD Vietsub, {{ $movie_detail->name }} tập 1, {{ $movie_detail->name }} tập cuối - Xem phim ngay tại TopFilm.">
    <meta name="keywords" content="{{ $movie_detail->meta }}">
    <title>{{ $movie_detail->name }} - FullHD Vietsub + Thuyết Minh</title>
    <link href="{{ asset('css/video-js.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/videojs-seek-buttons.css') }}" />
    <style>
        .vjs-menu-item-text {
            text-transform: none;
        }
    </style>
@endsection
@section('content')
    <section class="movie">
        <div class="movie_frame">
            <div class="movie_cover"></div>
            {{-- <div class="movie_cover1"></div>
			<div class="movie_cover2"></div> --}}
            <iframe src="https://loklok.com/detail/{{ $movie_detail->category }}/{{ $movie_detail->id }}"
                style="width: 100%; margin-top: 100px; height: 700px"></iframe>
        </div>

        <div class="box advanced">
            <div class="movie__container">
                <div class="movie__media" id="movie__media">
                    <input id="media" id_media="{{ $movie_detail->id }}" category="{{ $movie_detail->category }}"
                        id_episode="{{ $episode_id }}" class="hidden">
                    <video class="movie__screen video-js" id="video_media" preload="auto" data-setup="{}" controls autoplay>
                        <source src="movie" type="application/x-mpegURL">
                    </video>
                    <div class="movie__load">
                        <div id="loading_movie"></div>
                    </div>
                </div>
                <h1 class="movie__name" id="{{ $movie_detail['name'] }}">{{ $movie_detail->name }} - FullHD Vietsub +
                    Thuyết Minh</h1>
                <div class="movie__episodes">

                </div>
                <div class="movie__info">
                    <div class="movie__score"> <i class="fa-solid fa-star"></i> {{ $movie_detail->rate }}</div>
                    <div class="movie__year"> <i class="fa-solid fa-calendar"></i> {{ $movie_detail->year }}</div>
                </div>
                <div class="movie__tag"></div>
                <div class="movie__intro">{!! $movie_detail->description !!}</div>
                <div class="recommend__items__title">
                    <div class="recommend__items__name" style="max-width: 100%">
                        <span>Phim ngẫu nhiên</span>
                    </div>
                </div>
                <div class="recommend__item">
                    @foreach ($random_movies as $movie)
                        <a href="{{ route('detail_name', $movie->slug) }}" class="card__film">
                            <?php
                            if ($movie->image == '' || $movie->image == null) {
                                $url_image = asset('img/' . $movie->category . $movie->id . '.jpg');
                            } else {
                                $url_image = $movie->image;
                            }
                            ?>
                            <img class="image" src="{{ $url_image }}" alt="image" />
                            <p class="film__name">{{ $movie->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="movie__similar">
            </div>
        </div>
        <div class="box comments_hidden" style="display: none; margin-bottom: 20px">
            <div data-width="100%" class="fb-comments" data-href="{{ $url }}" data-width="" data-numposts="5">
            </div>
        </div>
    </section>
    <script src="{{ asset('js/video.min.js') }}"></script>
    <script src="{{ asset('js/videojs-seek-buttons.js') }}"></script>
    <script src="{{ asset('js/videojs-seek-buttons.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.movie__media').height($('.movie__media').width() * 1080 / 1920);
            $('.movie__load').height($('.movie__media').height() + 5);

            video = videojs('video_media');
            getVideo = setInterval(restart, 1000);

            if ('{{ $sub }}' != '') {
                fetch("{{ $sub }}").then((r) => {
                    r.text().then((d) => {
                        let srtText = d
                        var srtRegex =
                            /(.*\n)?(\d\d:\d\d:\d\d),(\d\d\d --> \d\d:\d\d:\d\d),(\d\d\d)/g;
                        var vttText = 'WEBVTT\n\n' + srtText.replace(srtRegex, '$1$2.$3.$4');
                        var vttBlob = new Blob([vttText], {
                            type: 'text/vtt'
                        });
                        var blobURL = URL.createObjectURL(vttBlob);
                        let captionOption = {
                            kind: 'captions',
                            srclang: 'vi',
                            label: 'Tiếng Việt',
                            src: blobURL
                        };
                        video.addRemoteTextTrack(captionOption);
                    })
                })
            }

            if ('{{ $sub_en }}' != '') {
                fetch("{{ $sub_en }}").then((r) => {
                    r.text().then((d) => {
                        let srtText = d
                        var srtRegex =
                            /(.*\n)?(\d\d:\d\d:\d\d),(\d\d\d --> \d\d:\d\d:\d\d),(\d\d\d)/g;
                        var vttText = 'WEBVTT\n\n' + srtText.replace(srtRegex, '$1$2.$3.$4');
                        var vttBlob = new Blob([vttText], {
                            type: 'text/vtt'
                        });
                        var blobURL = URL.createObjectURL(vttBlob);
                        let captionOption = {
                            kind: 'captions',
                            srclang: 'en',
                            label: 'Tiếng Anh',
                            src: blobURL
                        };
                        video.addRemoteTextTrack(captionOption);
                    })
                })
            }


            document.onkeydown = function(event) {
                switch (event.keyCode) {
                    case 37:
                        event.preventDefault();
                        vid_currentTime = video.currentTime();
                        video.currentTime(vid_currentTime - 5);
                        break;
                    case 39:
                        event.preventDefault();
                        vid_currentTime = video.currentTime();
                        video.currentTime(vid_currentTime + 5);
                        break;
                }
            };

            video.seekButtons({
                forward: 10,
                back: 10
            });

            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ route('movie.get-view-movie-ajax') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    name: "{{ $name }}",
                    episode_id: "{{ $episode_id }}",
                    _token: _token
                }
            }).done(function(data) {
                if ($('.movie__name').html() == '' || !data[8]) {
                    window.location.href = data[6];
                } else {
                    if (data[7]) {
                        $('.movie__name').html($('.movie__name').html() + " - Tập " +
                            "{{ $episode_id + 1 }}");
                    }
                }
                $('.movie__similar').html(data[1]);
                $('.comments_hidden').show();
                $('.movie__episodes').html(data[4]);
                $('.movie__tag').html(data[5]);
                return true;
            }).fail(function(e) {
                return false;
            });

            function restart() {
                if (video['cache_']['duration'] == 0 || !video['controls_'] || video['error_'] != null || isNaN(
                        video['cache_']['duration']) || video['cache_']['duration'] == 'Infinity') {
                    let episode_id = Number($('#media').attr('id_episode'));
                    let definition = $('.movie__quality').children(":selected").attr("id");
                    reload(episode_id, definition);
                } else {
                    $('.movie__load').hide();
                    if (video.textTracks()['tracks_'].length > 1) {
                        video.textTracks()[0].mode = 'showing';
                    }
                    clearInterval(getVideo);
                }
            }

            function reload(episode_id, definition) {
                let _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('movie.episode-ajax') }}",
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
                }).done(function(data) {
                    if (video['cache_']['duration'] == 0 || !video['controls_'] || video['error_'] !=
                        null || isNaN(video['cache_']['duration']) || video['cache_']['duration'] ==
                        'Infinity') {
                        video.src(data['mediaUrl']);
                    }
                    return true;
                }).fail(function(e) {
                    return false;
                });
            }
        })
    </script>
@endsection
