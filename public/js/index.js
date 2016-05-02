/**
 * Created by John Rich on 4/21/2016.
 */

$(document).ready(function(){
    $('input[type=text]').val('');
    function loadAirportsList(field, type){
        field.removeClass('field-error');
        var searchQuery = field.val();
         $.ajax({
            'url': '/loadAirportsList',
            'data': {'query': searchQuery},
            'headers': { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') },
            'method': 'post'
        }).done(function(data){
            var json = $.parseJSON(data);
            var htmlResponse = '';
            if(data !== '[]'){
                $.each(json, function(index, value){
                    var iata_faa;
                    if(value.iata_faa === undefined){
                        iata_faa = '***';
                    }else{
                        iata_faa = value.iata_faa;
                    }
                    htmlResponse += '<li id="' + value.id + '">' + value.name + ', ' + value.country + '(' + iata_faa +  ')</li>';
                });
                if(type === 'source'){
                    $('.source .available-options').html(htmlResponse).css({'visibility': 'visible'});
                }else if(type === 'destination'){
                    $('.destination .available-options').html(htmlResponse).css({'visibility': 'visible'});
                }
            }else{
                if(type === 'source'){
                    $('.source .available-options').html('').css({'visibility': 'hidden'});
                }else if(type === 'destination'){
                    $('.destination .available-options').html('').css({'visibility': 'hidden'});
                }
            }

            $('.source .available-options li').click(function(){
                var value = $(this).text();
                var route_id = $(this).attr('id');
                $('#source').attr('alt', route_id).val(value);
                $('.source .available-options').html('').css({'visibility': 'hidden'});
            });

            $('.destination .available-options li').click(function(){
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

    $('#source').on('input', function(){
        loadAirportsList($(this), 'source');
    });
    $('#destination').on('input', function(){
        loadAirportsList($(this), 'destination');
    });

    $('.submit').click(function(event){
        $(this).addClass('loading');
        $.ajax({
            'url': '/flights',
            'dataType': 'json',
            'data': {
                'source': $('input[name=source]').attr('alt'),
                'destination': $('input[name=destination]').attr('alt'),
                'airline': $('select[name=airline]').val(),
                'night_flight': $('input[name=night_flight]').parent().hasClass('checked') ? true : false,
                'relaxed_route': $('input[name=relaxed_route]').parent().hasClass('checked') ? true : false,
                'stops': $('input[name=stops]').parent().hasClass('checked') ? true : false,
                'stop_count': $('select[name=stop_count]').val()
            },
            'method' : 'post',
            'headers': { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') },
            'success': function(data){
                $('.submit').removeClass('loading');
                console.log(data);
            },
            'error': function(data){
                $('.submit').removeClass('loading');
                var errors = $.parseJSON(data.responseText);

                $.each(errors, function(index, value){
                    console.log(index);
                    console.log(value[0]);
                    if(index === 'source'){
                        $('#source').addClass('field-error').attr('placeholder', value[0]);
                    }else if(index === 'destination'){
                        $('#destination').addClass('field-error').attr('placeholder', value[0]);
                    }
                });
            }
        });
    });
});