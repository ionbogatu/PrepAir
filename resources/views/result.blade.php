<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 6/21/2016
 * Time: 2:25 PM
 */
?>

@if(!empty($result))
    {{ $json_string = json_encode($result, JSON_PRETTY_PRINT) }}
@endif