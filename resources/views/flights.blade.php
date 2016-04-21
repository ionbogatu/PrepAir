@extends ("layouts/layout")
@section("content")
<form action="retrieveResults.php" method="post">
    <label for="airline">
        Select airline.
    </label>
    <select id="airline">
        <option>None</option>
        <option>TAROM AIRLINES</option>
        <option>CARPAT AIR</option>
        <option>AIR FRANCE</option>
    </select>
    <label for="night_flights">
        Night Flight:
    </label>
    <input type="checkbox" name="nigth_flights" id="night_flights" />
    <label for="relax_route">
        The most relaxed route:
    </label>
    <input type="checkbox" name="relax_route" id="relax_route" />
    <label for="stopover">
        Routes with stopovers:
    </label>
    <input type="checkbox" name="stopover" id="stopover"/>
    <label for="stopover_time">
        Hours:
    </label>
    <input type="text" name="stopover_time" id="stopover_time" />
    <input type="submit" value="Search"/>

</form>
@endsection