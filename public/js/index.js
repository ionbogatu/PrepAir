/**
 * Created by John Rich on 4/21/2016.
 */

$(document).ready(function(){
    $('.ui.dropdown').dropdown();
    $('.ui.checkbox').checkbox();

    $('#stops').click(function(){
        var with_stops = $(this).parent().hasClass('checked') ? 'off' : 'on';
        if(with_stops == 'on'){
            $('.stop_hours').css('visibility', 'visible');
        }else if(with_stops == 'off'){
            $('.stop_hours').css('visibility', 'hidden');
        }
    });

    $('.button').click(function(){
        $(this).addClass('loading');
    });
});