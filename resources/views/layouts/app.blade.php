<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <script src="https://kit.fontawesome.com/b368ee8ced.js" crossorigin="anonymous"></script>
        <link href="{{ url('css/milligram.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <script type="text/javascript">
            // Fix for Firefox autofocus CSS bug
            // See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
        </script>
        <script type="text/javascript" src={{ url('js/app.js') }} defer>
        </script>
    </head>
    <body>
        <main>
            <header>
                <h1><a href="{{ url('/cards') }}">Jammer</a></h1>
                <div>
                    @if (Auth::check())
                        <a class="fa-regular fa-user fa-2xl" href="{{ url('/profile') }}"></a>
                        <a class="fa-regular fa-bell fa-2xl" href="{{ url('/notifications') }}" ></a>
                    @endif
                    <div class="dropdown" style="width: 1em;">
                        <div class="fa-solid fa-ellipsis-vertical fa-2xl three-dots-icon"></div>
                        <div class="dropdown-content">
                            <a href="{{ url('/aboutus') }}">About Us</a>
                            <a href="{{ url('/faq') }}">FAQ</a>
                            <a href="{{ url('/contacts') }}">Contacts</a>
                        </div>
                    </div>
                </div>
            </header>
            <section id="content">
                @yield('content')
            </section>
        </main>
    </body>
</html>