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
            <header class="empty"></header>
            <header class="topbar">
                <h1><a href="{{ url('/home') }}">Jammer</a></h1>
                <div>
                    @if (Auth::check())
                        @if (Auth::user()->image == "")
                            <a href="{{ url('/profile') }}"><img alt="Profile Picture" src="{{URL::asset('/images/default_pfp.png')}}" height="50" width="50" style="padding: none;" class="pfp"></a>
                        @else
                            <a href="{{ url('/profile') }}"><img alt="Profile Picture" src="{{URL::asset('storage/'.Auth::user()->image)}}" height="50" width="50" style="padding: none;" class="pfp"></a>
                        @endif
                        <a class="fa-regular fa-bell fa-2xl" href="{{ url('/notifications') }}" id="bell"></a>
                    @endif
                    <div class="dropdown">
                        <div class="fa-solid fa-bars fa-2xl hamburger-icon" id="burger"></div>
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