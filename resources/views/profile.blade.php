<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 4/25/2016
 * Time: 12:59 PM
 */
?>

@extends ('layouts.layout')
@section ('content')
    <script>
        function statusChangeCallback(response) {
            //console.log('statusChangeCallback');
            //console.log(response);
            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().
            if (response.status === 'connected') {
                // do nothing
                // the facebook account is already linked to our app
            } else if (response.status === 'not_authorized') {
                $('.fb-link-item').css({'display': 'block'});
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

            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });

        };
        // Load the SDK asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk/debug.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <br/>
    <br/>
    <div class="ui container profile-manager">
        <div class="ui form update-profile-data">
            <h4 class="ui dividing header">Personal information</h4>
            <div class="three fields">
                <div class="field">
                    <label>Name</label>
                    <input name="name" placeholder="{{ $user->name }}" type="text">
                </div>
                <div class="field"></div>
                <div class="field fb-link-item">
                    <p>
                        With your facebook account you can have access to all the functionalities that we offer.
                        To link your facebook account with our application, please click the button below.
                    </p>
                </div>
            </div>
            <br/>
            <div class="three fields">
                <div class="field">
                    <label>Change Password</label>
                    <input name="password" placeholder="New Password" type="password">
                </div>
                <div class="field">
                    <label>Confirm New Password</label>
                    <input name="confirm_password" placeholder="Confirm New Password" type="password">
                </div>
                <div class="field fb-link-item">
                    <label style="visibility: hidden;">Link with facebook</label>
                    <button class="ui blue button linkWithFB">Link with facebook</button>
                </div>
            </div>
            <br/>
            <div class="three fields">
                <div class="field">
                    <input name="submit" class="ui teal button update-profile-btn" value="Update profile" type="button">
                </div>
            </div>
            <div class="ui success message update-success">
                <div class="header">
                    Your profile has been updated successfully.
                </div>
            </div>
            <div class="ui error message update-error">
                <div class="header">
                    Failed to update your profile.
                </div>
            </div>
        </div>
        <h4 class="ui dividing header">Administrate preferences</h4>
        <div class="preferences">
            @if(isset($preferences))
                <table class="ui selectable inverted table user-preferences">
                    <thead>
                    <tr>
                        <th>Preference</th>
                        <th>Value</th>
                        <th class="right aligned">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($preferences as $preference)
                        <tr id="{{ $preference->id }}">
                            @if($preference->type_id == 1)
                                <td>
                                    Airport
                                </td>
                                <td>
                                    {{ $preference->value1->country }}, {{ $preference->value1->name }}, {{ isset($preference->value1->iata_faa) ? $preference->value1->iata_faa : $preference->value1->icao }}
                                </td>
                                <td class="right aligned">
                                    <!--i class="fa fa-pencil-square-o fa-lg update-preference" title="update"></i-->
                                    <i class="fa fa-times fa-lg delete-preference" title="delete"></i>
                                </td>
                            @elseif($preference->type_id == 2)
                                <td>
                                    Airline
                                </td>
                                <td>
                                    {{ $preference->value1->country }}, {{ $preference->value1->name }}, {{ isset($preference->value1->iata) ? $preference->value1->iata : $preference->value1->icao }}
                                </td>
                                <td class="right aligned">
                                    <!--i class="fa fa-pencil-square-o fa-lg update-preference" title="update"></i-->
                                    <i class="fa fa-times fa-lg delete-preference" title="delete"></i>
                                </td>
                            @elseif($preference->type_id == 3)
                                <td>
                                    Route
                                </td>
                                <td>
                                    {{ $preference->value1->country }}, {{ $preference->value1->name }}, {{ isset($preference->value1->iata_faa) ? $preference->value1->iata_faa : $preference->value1->icao }}
                                    <i class="dash fa fa-minus "></i>
                                    {{ $preference->value2->country }}, {{ $preference->value2->name }}, {{ isset($preference->value2->iata_faa) ? $preference->value2->iata_faa : $preference->value2->icao }}
                                </td>
                                <td class="right aligned">
                                    <!--i class="fa fa-pencil-square-o fa-lg update-preference" title="update"></i-->
                                    <i class="fa fa-times fa-lg delete-preference" title="delete"></i>
                                </td>
                            @elseif($preference->type_id == 4)
                                <td>
                                    Stops
                                </td>
                                <td>
                                    {{ $preference->value1 }}
                                </td>
                                <td class="right aligned">
                                    <!--i class="fa fa-pencil-square-o fa-lg update-preference" title="update"></i-->
                                    <i class="fa fa-times fa-lg delete-preference" title="delete"></i>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <a href="#" class="add-new add-preference">+Add new</a>
        <br/>
        <input name="submit" class="ui teal button submit-preferences" value="Save" type="submit">
        <div class="ui yellow message preference-message"></div>
    </div>
@endsection