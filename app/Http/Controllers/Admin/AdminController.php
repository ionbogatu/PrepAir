<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 4/23/2016
 * Time: 7:49 PM
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Storage;
use App\Route;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller{

    public function index(){
        return view('admin.index');
    }

    // --------------------------------

    private function importFiles(){
        $content = fopen('https://raw.githubusercontent.com/jpatokal/openflights/master/data/airports.dat', 'rb');
        Storage::disk('local')->put('airports.csv', $content);
        fclose($content);
        $content = fopen('https://raw.githubusercontent.com/jpatokal/openflights/master/data/airlines.dat', 'rb');
        Storage::disk('local')->put('airlines.csv', $content);
        fclose($content);
        $content = fopen('https://raw.githubusercontent.com/jpatokal/openflights/master/data/routes.dat', 'rb');
        Storage::disk('local')->put('routes.csv', $content);
        fclose($content);
    }

    private function isNotEmpty($string){
        if(
            empty($string) ||
            ($string == '') ||
            ($string == '\N') ||
            ($string == '""')
        ){
            return false;
        }

        return true;
    }

    private function populateAirportsAndAirlines($route){

        /*
         * Insert airline if not exists
         */

        if(Storage::disk('local')->exists('airlines.csv')) {
            $airlines = file(storage_path('app') . '\airlines.csv');
        }

        $airline_exists = DB::table('airlines')
            ->where('id', $route[1])
            ->first();

        if(!$airline_exists){
            foreach($airlines as $row){
                $airline = explode(',', $row);
                if($airline[0] == $route[1]){
                    if(!DB::table('airlines')->insert([
                        'id' => $airline[0],
                        'added_by' => null,
                        'name' => trim($airline[1], '"'),
                        'alias' => $this->isNotEmpty($airline[2]) ? trim($airline[2], '"') : null,
                        'iata' => $this->isNotEmpty($airline[3]) ? trim($airline[3], '"') : null,
                        'icao' => $this->isNotEmpty($airline[4]) ? trim($airline[4], '"') : null,
                        'country' => $this->isNotEmpty($airline[6]) ? trim($airline[6], '"') : null,
                        'active' => ($airline[7] == 'Y') ? 1 : 0
                    ])){
                        echo 'Cannot populate airline table with: ';
                        echo "<pre>";
                        var_dump($airline);
                        echo 'for this route';
                        var_dump($route);
                        die('airline');
                    }
                }
            }
        }

        unset($row, $airlines, $airline);

        /*
         * Insert source airport if not exists
         */

        if(Storage::disk('local')->exists('airports.csv')) {
            $airports = file(storage_path('app') . '\airports.csv');
        }

        $airport_exists = DB::table('airports')
            ->where('id', $route[3])
            ->first();

        if(!$airport_exists){
            foreach($airports as $row){
                $airport = explode(',', $row);
                if($airport[0] == $route[3]){
                    if(!DB::table('airports')->insert([
                        'id' => $airport[0],
                        'added_by' => null,
                        'name' => trim($airport[1], '"'),
                        'city' => $this->isNotEmpty($airport[2]) ? trim($airport[2], '"') : null,
                        'country' => $this->isNotEmpty($airport[3]) ? trim($airport[3], '"') : null,
                        'iata_faa' => $this->isNotEmpty($airport[4]) ? trim($airport[4], '"') : null,
                        'latitude' => $airport[6],
                        'longitude' => $airport[7],
                        'timezone' => $airport[9],
                        'daylight_saving_time' => trim($airport[10], '"'),
                    ])){
                        echo 'Cannot populate airport table with: ';
                        echo "<pre>";
                        var_dump($airport);
                        echo 'for this route';
                        var_dump($route);
                        die('source airport');
                    }
                }
            }
        }

        unset($row, $airport_exists, $airport);

        /*
         * Insert destination airport if not exists
         */

        $airport_exists = DB::table('airports')
            ->where('id', $route[5])
            ->first();

        if(!$airport_exists){
            foreach($airports as $row){
                $airport = explode(',', $row);
                if($airport[0] == $route[5]){
                    if(!DB::table('airports')->insert([
                        'id' => $airport[0],
                        'added_by' => null,
                        'name' => trim($airport[1], '"'),
                        'city' => $this->isNotEmpty($airport[2]) ? trim($airport[2], '"') : null,
                        'country' => $this->isNotEmpty($airport[3]) ? trim($airport[3], '"') : null,
                        'iata_faa' => $this->isNotEmpty($airport[4]) ? trim($airport[4], '"') : null,
                        'latitude' => $airport[6],
                        'longitude' => $airport[7],
                        'timezone' => $airport[9],
                        'daylight_saving_time' => trim($airport[10], '"'),
                    ])){
                        echo 'Cannot populate airport table with: ';
                        echo "<pre>";
                        var_dump($airport);
                        echo 'for this route';
                        var_dump($route);
                        die('destination airport');
                    }
                }
            }
        }

        unset($row, $airports);

        return true;
    }

    private function populateRoutes(){
        if(Storage::disk('local')->exists('routes.csv')) {
            $routes = file(storage_path('app') . '\routes.csv');
        }

        $preparedRows = array();

        $count = 0;

        foreach($routes as $row){
            $route = explode(',', $row);
            if(
                $this->isNotEmpty(trim($route[1], '"')) &&
                $this->isNotEmpty(trim($route[3], '"')) &&
                $this->isNotEmpty(trim($route[5], '"'))
            ) {
                $flight_is_valid = $this->populateAirportsAndAirlines($route);

                if (!$flight_is_valid) {
                    echo "Cannot populate this route<pre>";
                    var_dump($route);
                    continue;
                }

                $start = mt_rand(0, 86399);
                $end = mt_rand(0, 86399);

                $days_per_week = mt_rand(0, 6);
                $days = array();

                for($i = 0; $i <= $days_per_week; $i++){
                    $day = mt_rand(0, 6);
                    while(in_array($day, $days)){
                        $day = mt_rand(0, 6);
                    }
                    $days[] = $day;
                }

                sort($days, SORT_NUMERIC);

                while($start == $end){
                    $start = mt_rand(0, 86399);
                    $end = mt_rand(0, 86398);
                }

                if($start > $end){
                    $temp = $start;
                    $start = $end;
                    $end = $temp;
                }

                $preparedRows[] = array(
                    'id' => null,
                    'added_by' => null,
                    'airline_id' => $route[1],
                    'source_airport_id' => $route[3],
                    'destination_airport_id' => $route[5],
                    'departure_time' => $start,
                    'arrival_time' => $end,
                    'days' => json_encode($days),
                    'relaxed_note' => mt_rand(1, 5),
                    'stops' => $route[7],
                );
                $count++;
                if ($count % 6000 == 0) {
                    if (!DB::table('routes')->insert($preparedRows)) {
                        echo "Cannot populate routes table at: <pre>";
                        var_dump($route);
                        die('routes');
                    }

                    unset($preparedRows);
                }
            }
        }

        if(isset($preparedRows)) {
            if (!DB::table('routes')->insert($preparedRows)) {
                die('Cannot populate routes table');
            }
            unset($preparedRows);
        }
    }

    public function dbImport(){
        $start = time();

        $airports_exists = DB::table('airports')
            ->count();
        $airlines_exists = DB::table('airlines')
            ->count();
        $routes_exists = DB::table('routes')
            ->count();

        if(
            $airports_exists ||
            $airlines_exists ||
            $routes_exists
        ){
            return redirect()->route('admin_index')->with('response', 'The database is not empty')->with('response_type', 'error');
            die();
        }

        $this->importFiles();
        $this->populateRoutes();

        $log = '[ ' . date('Y-m-d H:i:s')  . ' ] - Repopulate DB tables: airports, airlines, routes';
        Storage::disk('log')->append('admin.log', $log);

        $end = time();

        echo "Total time: ";
        echo date('H:i:s', $end - $start);

        return redirect()->route('admin_index')->with('response', 'The database was refactored successfully')->with('response_type', 'success');
    }

    // --------------------------------

    

}