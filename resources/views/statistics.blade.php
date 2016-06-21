<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 6/20/2016
 * Time: 8:49 AM
 */
?>

@extends ('layouts.layout')

@section ('content')
    <br/>
    <br/>
    <div class="ui sixteen wide grid container">
        <div class="row">
            <div class="eight wide column">
                <h4 class="ui dividing header blue">Top 10 most searched routes</h4>
                {!! $top_10_most_searched_routes !!}
            </div>
            <div class="eight wide column">
                <h4 class="ui dividing header blue">Statistics</h4>
                <ul class="statistics-data">
                    <li>Number of routes: {{ $statistics['routes_counter'] }}</li>
                    <li>Number of airports: {{ $statistics['airports_counter'] }}</li>
                    <li>Number of airlines: {{ $statistics['airlines_counter'] }}</li>
                    <li>Number of users: {{ $statistics['users_counter'] }}</li>
                </ul>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="sixteen wide column">
                <h4 class="ui dividing header blue">Searched time</h4>
                {!! $grouped_routes !!}
            </div>
        </div>
    </div>
@endsection