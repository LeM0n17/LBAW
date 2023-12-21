<div id="search-bar">
    <form id="searchForm" method="get" action="{{ route('search') }}">
        <input id="search" type="text" placeholder="Search..." name="search" value="{{ Request::get('search') }}">
        <button type="submit">Search</button>
    </form>
</div>

