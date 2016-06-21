<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 6/21/2016
 * Time: 1:56 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApiController  extends Controller{
    public function index(){
        $result = [];

        if(isset($_GET['country'])){
            $result = DB::table('airports')
                ->where('country', htmlspecialchars($_GET['country'], ENT_QUOTES))
                ->get();
        }else if(
            isset($_GET['lat']) &&
            isset($_GET['lon'])
        ){
            $result = DB::select('select airports.*, pow((airports.latitude - ?),2) + pow((airports.longitude - ?),2) as distance from `airports` order by distance asc limit 1', [floatval(htmlspecialchars($_GET['lat'], ENT_QUOTES)), floatval(htmlspecialchars($_GET['lon'], ENT_QUOTES))]);
        }else if(
            isset($_GET['airport-code'])
        ){
            $result = DB::table('airports')
                ->where('iata_faa', htmlspecialchars($_GET['airport-code'], ENT_QUOTES))
                ->first();
        }else if(
            isset($_GET['airport-name'])
        ){
            $result = DB::table('airports')
                ->where('name', htmlspecialchars($_GET['airport-name'], ENT_QUOTES))
                ->first();
        }else if(
            isset($_GET['airport-id'])
        ) {
            $result = DB::table('airports')
                ->where('id', htmlspecialchars($_GET['airport-id'], ENT_QUOTES))
                ->first();
        }else if(
            isset($_GET['source-airport']) &&
            isset($_GET['destination-airport'])
        ){
            $result = DB::table('routes')
                ->where('source_airport_id', intval(htmlspecialchars($_GET['source-airport'], ENT_QUOTES)))
                ->where('destination_airport_id', intval(htmlspecialchars($_GET['destination-airport'], ENT_QUOTES)))
                ->first();
        }else{
            $result = [];
        }

        if(!empty($result)) {
            return view('result', ['result' => $result]);
        }else{
            return view('api');
        }
    }
}