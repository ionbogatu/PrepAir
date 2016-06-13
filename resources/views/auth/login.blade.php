@extends('layouts.app')

@section('content')
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '613779242110731',
            xfbml      : true,
            version    : 'v2.6'
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    function login(){
        FB.getLoginStatus(function(response){
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                // check if user has linked it's profile to our page
                FB.api('/me', 'get', function(user_data){
                    $.ajax({
                        'url': '/getUserByFBId',
                        'method': 'post',
                        'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
                        'data': {'response': response, 'user_data': user_data},
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
                });
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
            }
        });
    }
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
                                <button type="submit" class="btn btn-primary" style="display: block;">
                                    <i class="fa fa-btn fa-sign-in"></i>Login
                                </button>
                                <div id="facebookLogIn" class="social-networks-login-box">
                                    <fb:login-button data-scope="public_profile,email" onlogin="login">
                                    </fb:login-button>
                                </div>
                                <div id="googleSignIn" class="social-networks-login-box"></div>
                                <script>
                                    function onSuccess(googleUser) {
                                        console.log('Logged in as: ' + googleUser.getBasicProfile().getName());
                                    }
                                    function onFailure(error) {
                                        console.log(error);
                                    }
                                    function customGoogleSignInButton(){
                                        gapi.signin2.render('googleSignIn', {
                                            'scope': 'profile email',
                                            'width': 85,
                                            'height': 22,
                                            'longtitle': false,
                                            'theme': 'dark',
                                            'onsuccess': onSuccess,
                                            'onfailure': onFailure
                                        });
                                    }
                                </script>
                                <script src="https://apis.google.com/js/platform.js?onload=customGoogleSignInButton" async defer></script>
                                <br/><br/>
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
