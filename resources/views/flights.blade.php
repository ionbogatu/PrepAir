@extends ("layouts/layout")
@section("content")
<div class="ui container">
    <br/>
    <br/>
    {!! Form::open(array('url' => 'flights', 'class' => 'ui form')) !!}
        <h4 class="ui dividing header">Search Information</h4>
        <div class="three fields flight-data">
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
            <div class="field airline">
                {{ Form::label('', 'Airline') }}
                <select class="ui dropdown" name="airline">
                    <option value="" selected>Any</option>
                    @foreach ($airlines as $airline)
                        <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="field">
            <div class="fields">
                <div class="five wide field departure-wrapper">
                    {{ Form::label('departure-date', 'Departure date') }}
                        <div class="departure-date">
                            <input type="date" value="{{ date("Y-m-d", time()) }}" name="departure-date" id="departure-date"/>
                        </div>
                </div>
                <div class="one wide field">
                    <br/>
                    <div class="or">
                        Or
                    </div>
                </div>
                <div class="ten wide field prefered-days">
                    <label>Days you prefer to fly</label>
                    <div class="ui segment">
                        <div class="ui checkbox">
                            {{ Form::checkbox('mon', '', true, array('class' => 'hidden', 'tabindex' => 0, 'checked' => 'checked')) }}
                            {{ Form::label('mon', 'Monday') }}
                        </div>
                        <div class="ui checkbox">
                            {{ Form::checkbox('tue', '', true, array('class' => 'hidden', 'tabindex' => 0, 'checked' => 'checked')) }}
                            {{ Form::label('tue', 'Tuesday') }}
                        </div>
                        <div class="ui checkbox">
                            {{ Form::checkbox('wed', '', true, array('class' => 'hidden', 'tabindex' => 0, 'checked' => 'checked')) }}
                            {{ Form::label('wed', 'Wednesday') }}
                        </div>
                        <div class="ui checkbox">
                            {{ Form::checkbox('thu', '', true, array('class' => 'hidden', 'tabindex' => 0, 'checked' => 'checked')) }}
                            {{ Form::label('thu', 'Thursday') }}
                        </div>
                        <div class="ui checkbox">
                            {{ Form::checkbox('fri', '', true, array('class' => 'hidden', 'tabindex' => 0, 'checked' => 'checked')) }}
                            {{ Form::label('fri', 'Friday') }}
                        </div>
                        <div class="ui checkbox">
                            {{ Form::checkbox('sat', '', true, array('class' => 'hidden', 'tabindex' => 0, 'checked' => 'checked')) }}
                            {{ Form::label('sat', 'Saturday') }}
                        </div>
                        <div class="ui checkbox">
                            {{ Form::checkbox('sun', '', true, array('class' => 'hidden', 'tabindex' => 0, 'checked' => 'checked')) }}
                            {{ Form::label('sun', 'Sunday') }}
                        </div>
                    </div>
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
                        <option value="0" selected>Any</option>
                        @for ($i = 1; $i <= 3; $i++)
                            <option value="<?= $i; ?>">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="ui teal button submit" tabindex="0">Search for flights</div>
    {!! Form::close() !!}

    <!-- Hottest offers -->

    <div class="results"></div>
</div>
@endsection