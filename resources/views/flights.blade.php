@extends ("layouts/layout")
@section("content")
<div class="ui container">
    <br/>
    <br/>
    <div class="ui form">
        <h4 class="ui dividing header">Search Information</h4>
        <div class="field">
            <div class="three fields">
                <div class="field">
                    <label>Source</label>
                    <input name="source" placeholder="Source airport" type="text">
                </div>
                <div class="field">
                    <label>Destination</label>
                    <input name="destination" placeholder="Destination airport" type="text">
                </div>
                <div class="field">
                    <label>Airline</label>
                    <select class="ui dropdown" name="airline">
                        <option selected>Any</option>
                        @foreach ($airlines as $airline)
                            <option>{{ $airline->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="three fields">
            <div class="field">
                <div class="ui segment">
                    <div class="ui toggle checkbox">
                        <input class="hidden" tabindex="0" name="night_flights" type="checkbox">
                        <label>Night flight</label>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="ui segment">
                    <div class="ui toggle checkbox">
                        <input class="hidden" tabindex="0" name="relaxed_route" type="checkbox">
                        <label>The most relaxed route</label>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="ui segment">
                    <div class="ui toggle checkbox checked">
                        <input class="hidden" tabindex="0" name="stops" type="checkbox">
                        <label id="stops">Stops</label>
                    </div>
                    <select class="stop_hours">
                        <option selected>Any</option>
                        @for ($i = 0; $i <= 24; $i++)
                            <option>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="ui teal button" tabindex="0">Search for routes</div>
    </div>
</div>
@endsection