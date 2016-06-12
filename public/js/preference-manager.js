/**
 * Created by John Rich on 6/6/2016.
 */

function loadAirportsList(item){
    $.ajax({
        'url': '/loadAllAirportsList',
        'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
        'method': 'post',
        'success': function(data){
            var response = $.parseJSON(data);
            if(response.response !== '0') {
                var options = '<input name="airport" type="hidden">' +
                              '<i class="dropdown icon"></i>' +
                              '<div class="default text">Destination Airport</div>' +
                              '<div class="menu">';
                $.each(response.response, function(index, value){
                    options += '<div class="item" data-value="' + value.id + '">' + value.country + ', ' + value.name + ' (' + value.iata_faa + ')</div>';
                });
                options += '</div>';
                item.parent().parent().parent().children(".value").removeClass('input').addClass('selection dropdown').html(options);
                item.parent().parent().parent().children(".value2").remove();
                $('.ui.dropdown').dropdown();
            }
        }
    });
}

function loadAirlinesList(item){
    $.ajax({
        'url': '/loadAllAirlinesList',
        'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
        'method': 'post',
        'success': function(data){
            var response = $.parseJSON(data);
            if(response.response !== '0') {
                var options = '<input name="airline" type="hidden">' +
                    '<i class="dropdown icon"></i>' +
                    '<div class="default text">Airline</div>' +
                    '<div class="menu">';
                $.each(response.response, function(index, value){
                    var iata_icao = '';
                    if(value.iata !== null){
                        iata_icao = ' (' + value.iata + ')';
                    }else if(value.icao !== null){
                        iata_icao = ' (' + value.icao + ')';
                    }
                    options += '<div class="item" data-value="' + value.id + '">' + value.name + iata_icao + '</div>';
                });
                options += '</div>';
                item.parent().parent().parent().children(".value").removeClass('input').addClass('selection dropdown').html(options);
                item.parent().parent().parent().children(".value2").remove();
                $('.ui.dropdown').dropdown();
            }
            console.log(data);
        }
    });
}

function loadRoutesList(item){
    $.ajax({
        'url': '/loadAllAirportsList',
        'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
        'method': 'post',
        'success': function(data){
            var response = $.parseJSON(data);
            if(response.response !== '0') {
                var options = '<input name="source_id" type="hidden">' +
                    '<i class="dropdown icon"></i>' +
                    '<div class="default text">Source airport</div>' +
                    '<div class="menu">';
                var options2 = '<input name="destination_id" type="hidden">' +
                    '<i class="dropdown icon"></i>' +
                    '<div class="default text">Destination airport</div>' +
                    '<div class="menu">';
                $.each(response.response, function(index, value){
                    options += '<div class="item" data-value="' + value.id + '">' + value.country + ', ' + value.name + ' (' + value.iata_faa + ')</div>';
                    options2 += '<div class="item" data-value="' + value.id + '">' + value.country + ', ' + value.name + ' (' + value.iata_faa + ')</div>';
                });
                options += '</div>';
                options2 += '</div>';
                item.parent().parent().parent().children(".value2").remove();
                item.parent().parent().parent().children(".value").removeClass('input').addClass('selection dropdown').html(options).after('<div class="ui selection dropdown value2" style="float: left; margin-right: 10px;"></div>');
                item.parent().parent().parent().children(".value2").html(options2);
                $('.ui.dropdown').dropdown();
            }
        }
    });
}

function emptyTextField(item){
    item.parent().parent().parent().children('.value').addClass('input').removeClass('selection dropdown').remove();
    item.parent().parent().parent().children('.value2').remove();
    var emptyField = '<div class="ui input value">' +
                         '<input placeholder="Number of stops" type="text">' +
                     '</div>';
    item.parent().parent().parent().children('.dropdown').after(emptyField);
}

function prefereceActions(){
    $('.delete-preference').click(function(){
        var preference = $(this).parent().parent();
        var preference_id = preference.attr('id');
        $.ajax({
            'url': '/deletePreference',
            'method': 'post',
            'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
            'data': {
                'preference_id': preference_id
            },
            'success': function(data){
                //global preference;
                if(data === '1'){
                    var message = 'Failed to remove the preference';
                    $('.preference-message').fadeIn().text(message);
                    setTimeout(function(){
                        $('.preference-message').fadeOut();
                    }, 3000);
                }else if(data === '0'){
                    var message = 'The preference was removed';
                    $('.preference-message').fadeIn().text(message);
                    setTimeout(function(){
                        $('.preference-message').fadeOut();
                    }, 3000);
                    preference.remove();
                }
            }
        })
    });
    $('.update-preference').click(function(){
        // to do
    });
}

$(document).ready(function(){
    $('.add-preference').click(function(event){
        event.preventDefault();
        var preferenceTemplate = '<div class="preference-wrapper" id="">' +
                                     '<div class="ui selection dropdown preference-type" style="float: left; margin-right: 10px;">' +
                                         '<input name="preference" type="hidden">' +
                                         '<i class="dropdown icon"></i>' +
                                         '<div class="default text">Preference</div>' +
                                         '<div class="menu">' +
                                             '<div class="item" data-value="airport">Add Destination Airport</div>' +
                                             '<div class="item" data-value="airline">Add Airline</div>' +
                                             '<div class="item" data-value="route">Add Route</div>' +
                                             '<div class="item" data-value="stop">Add Stops</div>' +
                                         '</div>' +
                                     '</div>' +
                                     '<div class="ui selection dropdown value" style="float: left; margin-right: 10px;"></div>' +
                                     '<div class="actions">' +
                                        '<i class="fa fa-times" aria-hidden="true"></i>' +
                                     '</div>' +
                                     '<br/>' +
                                '</div>';
        $('.preferences').append(preferenceTemplate);
        $('.ui.dropdown').dropdown();

        $('.preference-wrapper .fa-times').click(function(){
            var preference = $(this).parent().parent();

            if(preference.attr('id') !== ''){
                $.ajax({
                    'url': '/deletePreference',
                    'method': 'post',
                    'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
                    'data': {'preference_id': preference.attr('id')},
                    'success': function(data){
                        if(data === '1'){
                            var message = 'Failed to remove the preference';
                            $('.preference-message').fadeIn().text(message);
                            setTimeout(function(){
                                $('.preference-message').fadeOut();
                            }, 3000);
                        }else if(data === '0'){
                            var message = 'The preference was removed';
                            $('.preference-message').fadeIn().text(message);
                            setTimeout(function(){
                                $('.preference-message').fadeOut();
                            }, 3000);
                            preference.remove();
                        }
                    }
                })
            }else{
                var message = 'The preference was removed';
                $('.preference-message').fadeIn().text(message);
                setTimeout(function(){
                    $('.preference-message').fadeOut();
                }, 3000);
                preference.remove();
            }
        });

        $('.preferences .item').click(function(){
            var key = $(this).attr('data-value');
            switch(key){
                case 'airport': loadAirportsList($(this));
                    break;
                case 'airline': loadAirlinesList($(this));
                    break;
                case 'route': loadRoutesList($(this));
                    break;
                case 'stop': emptyTextField($(this));
            }
        });
    });

    // submit preferences

    $('.submit-preferences').click(function(){
        var preferences = [];
        var preference_wrappers = $('.preference-wrapper');
        $.each(preference_wrappers, function(){
            var type = $(this).find('.preference-type').children('input[name=preference]').val();
            if(type === 'route'){
                var value1 = $(this).find('div.value').children('input').val();
                var value2 = $(this).find('div.value2').children('input').val();
                preferences.push({'type': type, 'value1': value1, 'value2': value2});
            }else {
                var value1 = $(this).find('div.value').children('input').val();
                preferences.push({'type': type, 'value1': value1});
            }
        });

        $.ajax({
            'url': '/addPreferences',
            'method': 'post',
            'headers': {'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')},
            'data': {
                'preferences': preferences
            },
            'dataType': 'json',
            'success': function(data){
                if(data != '1') {
                    var htmlResponse = '';
                    $.each(data, function(index, value){
                        var type = '';
                        var code = '';
                        var code2 = '';
                        var value_field = '';
                        if(value.type_id === 1) {
                            type = 'Airport';
                            if (value.value1.iata_faa !== null) {
                                code = value.value1.iata_faa;
                            } else if (value.value1.icao !== null) {
                                code = value.value1.icao;
                            }
                            value_field = '<td>' + value.value1.country + ', ' + value.value1.name + ', ' + code + '</td>';
                        }
                        else if(value.type_id === 2) {
                            type = 'Airline';
                            if(value.value1.iata !== null){
                                code = value.value1.iata;
                            }else if(value.value1.icao !== null){
                                code = value.value1.icao;
                            }
                            value_field = '<td>' + value.value1.country + ', ' + value.value1.name + ', ' + code + '</td>';
                        }
                        else if(value.type_id === 3) {
                            type = 'Route';
                            if(value.value1.iata_faa !== null){
                                code = value.value1.iata_faa;
                            }else if(value.value1.icao !== null){
                                code = value.value1.icao;
                            }

                            if(value.value2.iata_faa !== null){
                                code2 = value.value2.iata_faa;
                            }else if(value.value2.icao !== null){
                                code2 = value.value2.icao;
                            }

                            value_field = '<td>' + value.value1.country + ', ' + value.value1.name + ', ' + code +
                                          '<i class="dash fa fa-minus "></i>' +
                                          value.value2.country + ', ' + value.value2.name + ', ' + code2 + '</td>';
                        }
                        else if(value.type_id === 4) {
                            type = 'Stop';
                            value_field = '<td>' + value.value1 + '</td>';
                        }

                        htmlResponse = htmlResponse + '<tr id="' + value.id + '">' +
                                            '<td>' + type + '</td>' +
                                            value_field +
                                            '<td class="right aligned">' +
                                                '<!--i class="fa fa-pencil-square-o fa-lg update-preference" title="update"></i-->' +
                                                '<i class="fa fa-times fa-lg delete-preference" title="delete"></i>' +
                                            '</td>' +
                                        '</tr>';
                    });
                    console.log(htmlResponse);
                    $('.user-preferences > tbody').append(htmlResponse);
                    $('.preference-wrapper').remove();
                }

                // preference actions

                prefereceActions();
            }
        });
    });

    // preference actions

    prefereceActions();
});
