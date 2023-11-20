<div id="search-bar">
    <form method="get" action="{{ route('search') }}">
        <input type="text" placeholder="Search..." name="search" value="{{ Request::get('search') }}">
        <button type="submit">Search</button>
    </form>
</div>