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
                        <a class="button" href="{{ url('/profile') }}">Pro</a>
                        <a class="button">Not</a>
                    @endif
                    <div class="dropdown">
                        <div class="ham-menu"></div>
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