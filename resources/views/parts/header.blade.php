<?php
$url_slug = explode('/', Request::url());
$url_slug = $url_slug[count($url_slug) - 1];
?>
<div class="ui container fluid navigation">
    <div class="left-menu">
        <ul>
            @if(Auth::user())
            <li><a id="logout" href="/logout">Log Out</a></li>
            @else
            <li <?= ($url_slug === 'login') ? ' class="active"' : '' ?>><a href="/login">Log In</a></li>
            <li <?= ($url_slug === 'register') ? ' class="active"' : '' ?>><a href="/register">Register</a></li>
            @endif
        </ul>
    </div>
    <div class="right-menu">
        <ul>
            <li <?= ($url_slug === 'prepair.app') ? ' class="active"' : '' ?>><a href="/">Home</a></li>
            @if(Auth::user())
            <li <?= ($url_slug === 'profile') ? ' class="active"' : '' ?>><a href="/profile">Profile</a></li>
            @endif
            <li <?= ($url_slug === 'flights') ? ' class="active"' : '' ?>><a href="/flights">Flights</a></li>
            <li <?= ($url_slug === 'icalendar') ? ' class="active"' : '' ?>><a href="/icalendar">iCalendar</a></li>
            <li <?= ($url_slug === 'api') ? ' class="active"' : '' ?>><a href="/api">Api</a></li>
            <li <?= ($url_slug === 'statistics') ? ' class="active"' : '' ?>><a href="/statistics">Statistics</a></li>
            <li <?= ($url_slug === 'about') ? ' class="active"' : '' ?>><a href="/about">About</a></li>
        </ul>
    </div>
</div>