@extends('layouts.app')

@section('content')
<script>
    function statusChangeCallback(response) {
        //console.log('statusChangeCallback');
        //console.log(response);
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            // check if user has linked it's profile to our page
            $.ajax({
                'url': '/getUserByFBId',
                'method': 'post',
                'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
                'data': {'response': response},
                'dataType': 'json',
                'success': function(data){
                    if(data.response === 'connected'){
                        // user is connected
                        window.location.replace('/profile');
                    }else if(data.response === 'not_linked'){
                        // user is connected but his/her profile is not linked to our DB row
                        $('.socnet-message').html('To unlock all functionalities of our platform, please <a href="/login">Login</a> or <a href="/register">Register</a>, after that link your profile to your facebook account').css({'display': 'block'});
                    }else if(data.response === 'disconnected'){
                        // user is not connected
                    }
                }
            });
            //testAPI();
        } else if (response.status === 'not_authorized') {
            // The person is logged into Facebook, but not your app.
        } else {
            // The person is not logged into Facebook, so we're not sure if
            // they are logged into this app or not.
        }
    }

    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '613779242110731',
            cookie     : true,  // enable cookies to allow the server to access
                                // the session
            status     : true,  // get info about current user
            xfbml      : true,  // parse social plugins on this page
            version    : 'v2.5' // use graph api version 2.5
        });

        // Now that we've initialized the JavaScript SDK, we call
        // FB.getLoginStatus().  This function gets the state of the
        // person visiting this page and can return one of three states to
        // the callback you provide.  They can be:
        //
        // 1. Logged into your app ('connected')
        // 2. Logged into Facebook, but not your app ('not_authorized')
        // 3. Not logged into Facebook and can't tell if they are logged into
        //    your app or not.
        //
        // These three cases are handled in the callback function.

        /*FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });*/

    };
    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk/debug.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    // Here we run a very simple test of the Graph API after login is
    // successful.  See statusChangeCallback() for when this call is made.
    /*function testAPI() {
        /*FB.api('/me', function(response) {
         console.log('Successful login for: ' + response.name);
         });*\/
        FB.api('/me', function(response) {
            console.log(JSON.stringify(response));
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    var accessToken = response.authResponse.accessToken;
                    console.log({'access_token': accessToken});
                }
            } );
        });
    }*/
</script>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i>Login
                                </button>
                                <div class="social-networks-login-box">
                                    <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
                                    </fb:login-button>
                                </div>
                                <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
                                <script>
                                    function onSignIn(googleUser) {
                                        // Useful data for your client-side scripts:
                                        var profile = googleUser.getBasicProfile();
                                        console.log("ID: " + profile.getId()); // Don't send this directly to your server!
                                        console.log('Full Name: ' + profile.getName());
                                        console.log('Given Name: ' + profile.getGivenName());
                                        console.log('Family Name: ' + profile.getFamilyName());
                                        console.log("Image URL: " + profile.getImageUrl());
                                        console.log("Email: " + profile.getEmail());

                                        // The ID token you need to pass to your backend:
                                        var id_token = googleUser.getAuthResponse().id_token;
                                        console.log("ID Token: " + id_token);
                                    };
                                </script>
                                <div class="ui warning message socnet-message"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
