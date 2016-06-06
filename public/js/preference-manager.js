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
                              '<div class="default text">Airport</div>' +
                              '<div class="menu">';
                $.each(response.response, function(index, value){
                    options += '<div class="item" data-value="' + value.id + '">' + value.country + ', ' + value.name + ' (' + value.iata_faa + ')</div>';
                });
                options += '</div>';
                item.parent().parent().parent().children(".value").html(options);
                $('.ui.dropdown').dropdown();
            }
            console.log(options);
        }
    });
}

$(document).ready(function(){
    $('.add-preference').click(function(event){
        event.preventDefault();
        var preferenceTemplate = '<div class="preference-wrapper" id="">' +
                                     '<div class="ui selection dropdown" style="float: left; margin-right: 10px;">' +
                                         '<input name="preference" type="hidden">' +
                                         '<i class="dropdown icon"></i>' +
                                         '<div class="default text">Preference</div>' +
                                         '<div class="menu">' +
                                             '<div class="item" data-value="airport">Add Airport</div>' +
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
                    data: {'preference_id': preference.attr('id')},
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
            }
        });
    });
});
