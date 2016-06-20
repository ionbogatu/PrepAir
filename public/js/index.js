/**
 * Created by John Rich on 4/21/2016.
 */

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

$(document).ready(function(){
    $('input[type=text]').val('');
    function loadAirportsList(field, type){
        field.removeClass('field-error');
        var searchQuery = field.val();
        $.ajax({
            'url': '/loadAirportsList',
            'data': {'query': searchQuery},
            'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
            'method': 'post'
        }).done(function (data) {
            var json = $.parseJSON(data);
            var htmlResponse = '';
            if (data !== '[]') {
                $.each(json, function (index, value) {
                    var iata_faa;
                    if (value.iata_faa === undefined) {
                        iata_faa = '***';
                    } else {
                        iata_faa = value.iata_faa;
                    }
                    htmlResponse += '<li id="' + value.id + '">' + value.name + ', ' + value.country + '(' + iata_faa + ')</li>';
                });
                if (type === 'source') {
                    $('.source .available-options').html(htmlResponse).css({'visibility': 'visible'});
                } else if (type === 'destination') {
                    $('.destination .available-options').html(htmlResponse).css({'visibility': 'visible'});
                }
            } else {
                if (type === 'source') {
                    $('.source .available-options').html('').css({'visibility': 'hidden'});
                } else if (type === 'destination') {
                    $('.destination .available-options').html('').css({'visibility': 'hidden'});
                }
            }

            $('.source .available-options li').click(function () {
                var value = $(this).text();
                var route_id = $(this).attr('id');
                $('#source').attr('alt', route_id).val(value);
                $('.source .available-options').html('').css({'visibility': 'hidden'});
            });

            $('.destination .available-options li').click(function () {
                var value = $(this).text();
                var route_id = $(this).attr('id');
                $('#destination').attr('alt', route_id).val(value);
                $('.destination .available-options').html('').css({'visibility': 'hidden'});
            });
        });
    }

    $('.ui.dropdown').dropdown();
    $('.ui.checkbox').checkbox();

    $('#stops').click(function(){
        var with_stops = $(this).parent().hasClass('checked') ? 'off' : 'on';
        if(with_stops == 'on'){
            $('.stop_count').css('visibility', 'visible');
        }else if(with_stops == 'off'){
            $('.stop_count').css('visibility', 'hidden');
        }
    });

    $('#source, #destination').on('blur', function(){
        if($(this).val() === ''){
            $(this).addClass('field-error');
        }else{
            $(this).removeClass('field-error');
        }
    });

    $('#source').on('input', function(){
        loadAirportsList($(this), 'source');
    });
    $('#destination').on('input', function(){
        loadAirportsList($(this), 'destination');
    });

    $('.submit').click(function(event){
        $(this).addClass('loading');
        var days = [];
        var d = $('.prefered-days input');
        var counter = 0;
        $.each(d, function(index, value){
            days[counter] = {name: value.name, value: value.checked};
            counter++;
        });

        $.ajax({
            'url': '/flights',
            'dataType': 'json',
            'data': {
                'source': $('input[name=source]').attr('alt'),
                'destination': $('input[name=destination]').attr('alt'),
                'airline': $('select[name=airline]').val(),
                'date': $('input[name=departure-date]').val(),
                'days': days,
                'night_flight': $('input[name=night_flight]').parent().hasClass('checked') ? true : false,
                'relaxed_route': $('input[name=relaxed_route]').parent().hasClass('checked') ? true : false,
                'stops': $('input[name=stops]').parent().hasClass('checked') ? true : false,
                'stop_count': $('select[name=stop_count]').val()
            },
            'method' : 'post',
            'headers': { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') },
            'success': function(data){
                console.log(data);
                if(data.success == 0){
                    if($('#source').val() == ''){
                        $('#source').addClass('field-error');
                    }
                    if($('#destination').val() == ''){
                        $('#destination').addClass('field-error');
                    }
                }else if(data.success == 1){
                    $('#departure-date').addClass('field-error');
                    $('.prefered-days > .segment').addClass('field-error');
                }else{
                    $('#source').removeClass('field-error');
                    $('#destination').removeClass('field-error');
                    $('#departure-date').removeClass('field-error');
                    $('.prefered-days > .segment').removeClass('field-error');

                    // append results

                    $('.results').html(data);
                }
                $('.submit').removeClass('loading');
            }
        });
    });

    $('.update-profile-btn').click(function(){
        var data = {};
        var name = $('.update-profile-data input[name="name"]').val();
        var password = $('.update-profile-data input[name="password"]').val();
        var confirm_password = $('.update-profile-data input[name="confirm_password"]').val();
        var latitude = $('#us2-lat').val();
        var longitude = $('#us2-lon').val();

        if(name !== ''){
            data.name = name;
        }
        if(
            password !== '' &&
            confirm_password !== ''
        ){
            if(password === confirm_password){
                data.password = password;
                data.confirm_password = confirm_password;
            }else{
                $('.location-message').removeClass('positive').addClass('negative').text('Oops! Something went wrong. Confirm password doesn\'t match').css({'display': 'block'});
                setTimeout(function(){
                    $('.location-message').css({'display': 'none'});
                }, 3000);
            }
        }

        if(
            latitude < 180 &&
            latitude > -180 &&
            longitude < 180 &&
            longitude > -180
        ){
            data.latitude = latitude;
            data.longitude = longitude;
        }else{
            console.log(latitude, longitude);
            $('.location-message').removeClass('positive').addClass('negative').text('Oops! Something went wrong. Wrong coordinates').css({'display': 'block'});
            setTimeout(function(){
                $('.location-message').css({'display': 'none'});
            }, 3000);
        }

        if(!$.isEmptyObject(data)){
            $.ajax({
                'url': '/updatePersonalInformation',
                'method': 'post',
                'headers': { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') },
                'data': {'data': data},
                'success': function(data){
                    console.log(data);
                    if(data == 1){
                        $('.update-success').css({'display': 'block'});
                        setTimeout(function(){
                            $('.update-success').css({'display': 'none'});
                        }, 3000);
                        $('.update-error').css({'display': 'none'});
                    }else if(data == 0){
                        $('.update-success').css({'display': 'none'});
                        $('.update-error').css({'display': 'block'});
                        setTimeout(function(){
                            $('.update-error').css({'display': 'none'});
                        },3000);
                    }
                }
            })
        }
    });

    $('.linkWithFB').click(function(){
        FB.api('/me', function(response){
            console.log(response);
            $.ajax({
                'url': '/linkWithFacebook',
                'method': 'post',
                'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
                'data': {'response': response},
                'dataType': 'json',
                'beforeSend': function(){
                    $('.linkWithFB').addClass('loading');
                },
                'success': function(data){
                    if(data.response == 1){
                        // ok
                        $('.linkWithFB').removeClass('loading');
                        $('.fb-link-item').remove();
                    }else if(data.response == 0){
                        // cannot update
                        $('.fb-link-message').addClass('error-message').html('A fatal error occured. Please contact the administrator: <a href="mailto:ionbogatu@gmail.com">Bogatu Ion</a>')
                        $('.linkWithFB').removeClass('loading');
                    }else if(data.response == 2){
                        // profile already linked
                        $('.fb-link-message').addClass('error-message').html('We are sorry, but this facebook account has been linked to another user. Please contact the administrator: <a href="mailto:ionbogatu@gmail.com">Bogatu Ion</a>')
                        $('.linkWithFB').removeClass('loading');
                    }
                },
                'error': function(){
                    $('.fb-link-message').addClass('error-message').html('A fatal error occured. Please contact the administrator: <a href="mailto:ionbogatu@gmail.com">Bogatu Ion</a>')
                    $('.linkWithFB').removeClass('loading');
                }
            })
        });
    });

    $('#logout').click(function(event){
        event.preventDefault();
        FB.getLoginStatus(function(response) {
            if(
                response.status === 'connected'
            ){
                FB.logout(function(response){});
            }
        });
        window.location.replace('/logout');
    });
});
