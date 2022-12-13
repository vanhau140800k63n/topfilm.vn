<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <link rel="shortcut icon" href="{{ asset('img/logo1.png') }}" />
    <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('css/swiper-bundle.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/videojs-seek-buttons.css')}}" />
    @yield('head')

    <?php
    header('Access-Control-Allow-Origin: *');
    ?>
</head>

<body>
    @include('partial.header')
    @yield('content')
    @include('partial.footer')

    <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
    <script type="text/javascript">
        $('.image').css('max-height', $('.card__film').width() * 1.4);
        getImage();
        getMovieData();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getImage() {
            $.ajax({
                url: "{{ route('storage-ajax') }}",
                type: "GET",
                dataType: 'json',
            }).done(function(data) {
                setTimeout(function() {
                    getImage();
                }, 2000);
                return true;
            }).fail(function(e) {
                return false;
            });
        }

        function getMovieData() {
            $.ajax({
                url: "{{ route('storage-movie-ajax') }}",
                type: "GET",
                dataType: 'json',
            }).done(function(data) {
                setTimeout(function() {
                    getMovieData();
                }, 2000);
                return true;
            }).fail(function(e) {
                return false;
            });
        }
    </script>
</body>

</html>