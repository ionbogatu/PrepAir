/**
 * Created by John Rich on 4/23/2016.
 */

$(document).ready(function(){
    $('.db_import').click(function(event){
        event.preventDefault();
        response = confirm('Are you sure you want to remake the database? The full remake can take an hour');
        if(response){
            location.replace("http://prepair.app/admins/db/import");
        }
    });
});