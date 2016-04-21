<div class="ui container fluid navigation">
    <div class="left-menu">
        <ul>
            @if(Auth::user())
            <li><a id="logout" href="/logout">Log Out</a></li>
            @else
            <li><a href="/login">Log In</a></li>
            <li><a href="/register">Register</a></li>
            @endif
        </ul>
    </div>
    <div class="right-menu">
        <ul>
            <li class="active"><a href="/">Home</a></li>
            @if(Auth::user())
            <li><a href="/profile">Profile</a></li>
            @endif
            <li><a href="/resources">Routes</a></li>
            <li><a href="/icalendar">iCalendar</a></li>
            <li><a href="/api">Api</a></li>
            <li><a href="/statistics">Statistics</a></li>
            <li><a href="/about">About</a></li>
        </ul>
    </div>
</div>