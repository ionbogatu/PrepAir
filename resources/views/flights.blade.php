@extends ("layouts/layout")
@section("content")
<div class="ui container">
    <br/>
    <br/>
    {!! Form::open(array('url' => 'flights', 'class' => 'ui form')) !!}
        <h4 class="ui dividing header">Search Information</h4>
        <div class="three fields">
            <div class="field">
                <div class="field-wrapper source">
                    {{ Form::label('source', 'Source') }}
                    {{ Form::text('source', '', array('placeholder' => 'Source airport')) }}
                    <ul class="available-options"></ul>
                </div>
            </div>
            <div class="field">
                <div class="field-wrapper destination">
                    {{ Form::label('destination', 'Destination') }}
                    {{ Form::text('destination', '', array('placeholder' => 'Destination airport')) }}
                    <ul class="available-options"></ul>
                </div>
            </div>
            <div class="field">
                {{ Form::label('', 'Airline') }}
                <select class="ui dropdown" name="airline">
                    <option selected>Any</option>
                    @foreach ($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="three fields">
            <div class="field">
                <div class="field-wrapper departure-date">
                    {{ Form::label('departure-date', 'Departure date') }}
                    <input type="date" value="{{ date("Y-m-d", time()) }}" id="departure-date"/>
                </div>
            </div>
            <div class="field">
                <div class="field-wrapper arrival-date">
                    {{ Form::label('arrival-date', 'Arrival date') }}
                    <input type="date" value="{{ date("Y-m-d", strtotime('+1 week')) }}" id="arrival-date"/>
                </div>
            </div>
            <div class="field">
                <div class="field-wrapper arrival-date">
                    <input type="hidden"/>
                </div>
            </div>
        </div>
        <div class="three fields checkboxes">
            <div class="field">
                <label>Day period</label>
                <div class="ui segment">
                    <div class="ui toggle checkbox">
                        {{ Form::checkbox('night_flights', '', false, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('night_flight', 'Night flight') }}
                    </div>
                </div>
            </div>
            <div class="field">
                <label>Flight type</label>
                <div class="ui segment">
                    <div class="ui toggle checkbox">
                        {{ Form::checkbox('relaxed_route', '', false, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('relaxed_route', 'The most relaxed flight') }}
                    </div>
                </div>
            </div>
            <div class="field">
                <label>Stops number</label>
                <div class="ui segment">
                    <div class="ui toggle checkbox checked">
                        {{ Form::checkbox('stops', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('stops', 'Stops', array('id' => 'stops')) }}
                    </div>
                    <select name="stop_count" class="stop_count">
                        <option selected>Any</option>
                        @for ($i = 0; $i <= 3; $i++)
                            <option>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="one field prefered-days">
            <div class="field">
                <label>Days you prefer to fly</label>
                <div class="ui segment">
                    <div class="ui checkbox">
                        {{ Form::checkbox('mon', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('mon', 'Monday') }}
                    </div>
                    <div class="ui checkbox">
                        {{ Form::checkbox('tue', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('tue', 'Tuesday') }}
                    </div>
                    <div class="ui checkbox">
                        {{ Form::checkbox('wed', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('wed', 'Wednesday') }}
                    </div>
                    <div class="ui checkbox">
                        {{ Form::checkbox('thu', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('thu', 'Thursday') }}
                    </div>
                    <div class="ui checkbox">
                        {{ Form::checkbox('fri', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('fri', 'Friday') }}
                    </div>
                    <div class="ui checkbox">
                        {{ Form::checkbox('sat', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('sat', 'Saturday') }}
                    </div>
                    <div class="ui checkbox">
                        {{ Form::checkbox('sun', '', true, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('sun', 'Sunday') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="ui teal button submit" tabindex="0">Search for flights</div>
    {!! Form::close() !!}
</div>
@endsection