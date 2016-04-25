 
@extends ('layouts.layout')
@section ('content')
    <div class="ui container" >
        <div class="ui form">
            <div class="three fields">
                <div class="field">
                    <label>Name</label>
                    <input name="name" placeholder="{{ $user->name }}" type="text"></input>
                </div>
                
            </div>
            <div class="three fields">
                <div class="field">
                    <label>Password</label>
                    <input name="password" value="password" type="password"></input>
                </div>
                 <div class="field">
                    <label>Confirm Password</label>
                    <input name="confirm_password" placeholder="Confirm password" type="password"></input>
                </div>
                
            </div>
            <div class="three fields">
                <div class="field">
                    <input name="submit" type="submit" class="ui teal button" value="Update profile"></input>
                </div>
                
            </div>
        </div>
    </div>
@endsection