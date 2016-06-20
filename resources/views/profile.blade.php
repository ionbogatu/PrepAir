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
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '613779242110731',
                xfbml      : true,
                version    : 'v2.6'
            });
            checkLoginStatus();
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function checkLoginStatus(){
            FB.getLoginStatus(function(response){
                console.log(response);
                if (response.status === 'connected') {
                    // Logged into your app and Facebook.
                    // check if user has linked it's profile to our page
                    FB.api('/me', function(user_data){
                        console.log(user_data);
                        $.ajax({
                            'url': '/getUserByFBId',
                            'method': 'post',
                            'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
                            'data': {'response': response, 'user_data': user_data},
                            'dataType': 'json',
                            'success': function(data){
                                console.log(data);
                                if(data.response === 'connected'){
                                    // user is connected
                                    $('.fb-link-item').css({'display': 'none'});
                                }else if(data.response === 'not_linked'){
                                    // user is connected but his/her profile is not linked to our DB row
                                    $('.fb-link-item').css({'display': 'block'});
                                }else if(data.response === 'disconnected'){
                                    // user is not connected
                                    $('.fb-link-item').css({'display': 'none'});
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
                <div class="field fb-link-item fb-link-message">
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
            <div class="three fields">
                <div class="field">
                    <label>Location:</label>
                    <input type="text" class="form-control" id="us2-address" />
                </div>
                <div class="field">
                    <label>Radius:</label>
                    <input type="text" class="form-control" id="us2-radius" />
                </div>
            </div>
            <div class="ui message location-message"></div>
            <div class="twelve fields location-picker-wrapper">
                <div id="us2" style="width: 100%; height: 400px;"></div>
            </div>
            <br/>
            <div class="three fields">
                <div class="field">
                    <label>Lat:</label>
                    <input type="text" class="form-control" id="us2-lat" />
                </div>
                <div class="field">
                    <label>Lon:</label>
                    <input type="text" class="form-control" id="us2-lon" />
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