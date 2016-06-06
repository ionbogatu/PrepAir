<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 6/6/2016
 * Time: 12:40 AM
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class TestController extends Controller{
    public function testWeather(){
        $client = new Client(['headers' => ['x-csrf' => csrf_token()]]);
        $lat = -6.08168900;
        $lon = 145.39188100;
        $response = $client->request('GET', "http://api.openweathermap.org/data/2.5/forecast/daily?lat=" . intval($lat) . "&lon=" . $lon . "&cnt=10&mode=json&appid=30a1509ebfe4eb04e7c3d5db8ed97502");

        echo '<pre>';
        $data = json_decode((string)$response->getBody());
        var_dump($data);

        $temps = $data->list;

        $prev_temp = '';
        $i = 0;
        while($temps[$i]->dt < 1465344000){
            $prev_temp = $temps[$i];
            $i++;
        }

        var_dump($temps);
        var_dump($prev_temp);
    }
}