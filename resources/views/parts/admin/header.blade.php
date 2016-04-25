<div class="ui container fluid navigation">
    <div class="left-menu">
        <ul>
            @if(Auth::user())
                <li><a id="logout" href="/logout">Log Out</a></li>
                <li><a href="/">Home</a></li>
            @endif
        </ul>
    </div>
</div>