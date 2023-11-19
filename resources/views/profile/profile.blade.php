@extends('layouts.app')

@section('content')
    <link href="{{ url('css/static.css') }}" rel="stylesheet">
    
    <div class="main">
        <h2>About Us</h2>
        <div class="aboutus-text">
            Jammer is a dedicated event management website aimed at game developers seeking to compete in Game Jams1 or host their own. This product is being developed by a small team of software engineers at the request of FEUP.
            <br><br>
            For hosts, Jammer strives to facilitate the convoluted and tedious process of organizing and publicizing a Game Jam. For developers, its main goal is establishing a unified platform for discovering the latest Game Jams. For hosts, it provides a streamlined system for managing their competitions.
            <br><br>
            Jammer has three distinct user categories: guests, registered users, and administrators. Guests are users who are not signed in and, as such, cannot access restricted content. Registered users, as the name implies, are those with an account, allowing them to freely browse the website and take advantage of its functionalities. Finally, administrators are special users entrusted with regulating user-submitted content and ensuring the website’s integrity, meaning that they have additional moderation permissions.
            <br><br>    
            All registered users can create a Game Jam and configure it to their liking. Several parameters can be customized, including the name of the contest, the theme, the description, the start and end dates, and the number of participants, to name a few. Upon publishing the Game Jam, a dedicated page is created containing the following tabs: an information tab, which displays the details of the event, a forum tab, where users can interact with each other through comments and polls, and a submission tab, where Jam participants can submit their game.
            <br><br>
            In addition, users can join any ongoing public Game Jam. Coupled with an effortlessly intuitive search system that allows filtering over the aforementioned parameters, users are always guaranteed to find an event that meets their preferences.
            <br><br>
            Jammer also offers a highly versatile and interactive user experience. In fact, users can update their accounts anytime, by changing public (name, profile picture, etc.) and private (password) information. What’s more, Game Jam hosts can take advantage of the website’s rich event visibility system to thoroughly control who joins their competitions.
        </div>
    </div>
@endsection