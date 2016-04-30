@extends ("layouts/layout")
@section("content")
<div class="ui container">
    <br/>
    <br/>
    {!! Form::open(array('url' => 'flights', 'class' => 'ui form')) !!}
        <h4 class="ui dividing header">Search Information</h4>
        <div class="three fields">
            <div class="field">
                {{ Form::label('source', 'Source') }}
                {{ Form::text('source', '', array('placeholder' => 'Source airport')) }}
            </div>
            <div class="field">
                {{ Form::label('destination', 'Destination') }}
                {{ Form::text('destination', '', array('placeholder' => 'Destination airport')) }}
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
                <div class="ui segment">
                    <div class="ui toggle checkbox">
                        {{ Form::checkbox('night_flights', '', false, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('night_flight', 'Night flight') }}
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="ui segment">
                    <div class="ui toggle checkbox">
                        {{ Form::checkbox('relaxed_route', '', false, array('class' => 'hidden', 'tabindex' => 0)) }}
                        {{ Form::label('relaxed_route', 'The most relaxed route') }}
                    </div>
                </div>
            </div>
            <div class="field">
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
        <div class="ui teal button submit" tabindex="0">Search for flights</div>
    {!! Form::close() !!}
</div>
@endsection