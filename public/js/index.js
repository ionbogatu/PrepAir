/**
 * Created by John Rich on 4/21/2016.
 */

$(document).ready(function(){
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

    $('.submit').click(function(event){
        event.preventDefault();
        $(this).addClass('loading');
        $.ajax({
            'url': '/flights',
            'data': {
                'source': $('input[name=source]').val(),
                'destination': $('input[name=destination]').val(),
                'airline': $('select[name=airline]').val(),
                'night_flight': $('input[name=night_flight]').val(),
                'relaxed_route': $('input[name=relaxed_route]').val(),
                'stops': $('input[name=stops]').val(),
                'stop_count': $('select[name=stop_count]').val()
            },
            'method' : 'post',
            'headers': { 'X-CSRF-Token' : $('meta[name=csrf-token]').attr('content') }
        }).done(function(data){
            $('.submit').removeClass('loading');
            alert(data);
        });
    });
});