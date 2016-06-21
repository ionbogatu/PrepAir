<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 6/21/2016
 * Time: 1:59 PM
 */
?>

@extends ('layouts.layout')

@section ('content')
    <h4>Usage: </h4>
    <ul>
        <li>http://prepair.app/api?country=country_name</li>
        <li>Returns all airports form the country</li>
        <li>http://prepair.app/api?lat=latitude&lon=longitude</li>
        <li>Returns nearest airport according to specified latitude and longitude</li>
        <li>http://prepair.app/api?airport-code=code</li>
        <li>Where code is airport's iata or faa code</li>
        <li>Returns the airport according to specified code</li>
        <li>http://prepair.app/api?airport-name=airport_name</li>
        <li>Returns the airport according to specified name</li>
        <li>http://prepair.app/api?airport-id=airport_id</li>
        <li>Returns the airport according to specified airport id</li>
        <li>http://prepair.app/api?source-airport=source_airport&destination-airport=destination_airport</li>
        <li>Where source_airport and destination_airport are the ids of the source airport and destination airport respectively</li>
        <li>Returns the route according to specified source airport id and destination airport id</li>
    </ul>
@endsection