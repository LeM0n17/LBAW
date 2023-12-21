<div id="search-bar">
    <form id="searchForm" method="get" action="{{ route('search') }}">
        <input id="search" type="text" placeholder="Search..." name="search" value="{{ Request::get('search') }}">
        <button type="submit">Search</button>
    </form>
</div>
<div id ="filters">
    <form id="dateForm" method="get" action="{{ route('filterByDate') }}">
        @csrf
        <p>List all the events that start after given date:</p>
        <input id="date" type="date" name="date" placeholder=>
        <button type="submit">Filter by Date</button>
    </form>
    <form id="tagForm" method="get" action="">
        @csrf
        <input id="tag" type="text" placeholder="Tag" name="tag">
        <button type="submit">Filter by Tag</button>
    </form>    
</div>