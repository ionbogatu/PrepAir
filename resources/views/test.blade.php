<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 6/19/2016
 * Time: 9:15 PM
 */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <!-- -->

    <!-- Location picker -->
    <script type="text/javascript" src='http://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
    <script src="/jquery-picker/dist/locationpicker.jquery.js"></script>
</head>

<body>
<div class="container">
    <div id="examples">
        <p>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-1 control-label">Location:</label>

                <div class="col-sm-5">
                    <input type="text" class="form-control" id="us2-address" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-1 control-label">Radius:</label>

                <div class="col-sm-2">
                    <input type="text" class="form-control" id="us2-radius" />
                </div>
            </div>
            <div id="us2" style="width: 550px; height: 400px;"></div>
            <div class="clearfix">&nbsp;</div>
            <div class="m-t-small">
                <label class="p-r-small col-sm-1 control-label">Lat.:</label>

                <div class="col-sm-1">
                    <input type="text" class="form-control" style="width: 110px" id="us2-lat" />
                </div>
                <label class="p-r-small col-sm-1 control-label">Long.:</label>

                <div class="col-sm-1">
                    <input type="text" class="form-control" style="width: 110px" id="us2-lon" />
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <script>
            $('#us2').locationpicker({
                location: {
                    latitude: 46.15242437752303,
                    longitude: 2.7470703125
                },
                radius: 300,
                inputBinding: {
                    latitudeInput: $('#us2-lat'),
                    longitudeInput: $('#us2-lon'),
                    radiusInput: $('#us2-radius'),
                    locationNameInput: $('#us2-address')
                },
                enableAutocomplete: true
            });
        </script>
    </div>
    <footer>
        <p class="pull-right">
            <a href="#start">Back to top</a>
        </p>

        <p>
            <a href="http://logicify.com/" target="_blank">Logicify</a>
        </p>
    </footer>
</div>
</body>

</html>