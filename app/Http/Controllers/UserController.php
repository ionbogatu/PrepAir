<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 4/18/2016
 * Time: 10:09 PM
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Khill\Lavacharts\Laravel\LavachartsFacade as Lava;
use Monolog\Logger as Log;
use GuzzleHttp\Client;
use App\User;

class UserController extends Controller{

    private $logObj = '';

    /**
     *
     */

    public function __construct(){
        if(empty($this->logObj)){
            $this->logObj = new Log('app');
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function flights(){
        $airlines = DB::table('airlines')
            ->orderBy('name')
            ->get();

        $user = Auth::user();

        $most_searched_routes = [];

        if(isset($user)){
            $most_searched_routes = $this->getMostSearchedRoutesByLocation($user->latitude, $user->longitude);
        }

        return view('flights', ['airlines' => $airlines, 'most_searched_routes' => $most_searched_routes]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function statistics(){

        // render top searched chart

        $routes = Lava::DataTable();

        $response = DB::select('SELECT a1.`name` as name1, a1.country as country1, a2.name as name2, a2.country as country2, routes.departure_time, routes.arrival_time, count(statistics.id) as \'count\' FROM `statistics` join routes on statistics.route_id = routes.id join airports a1 on routes.source_airport_id = a1.id join airports a2 on routes.destination_airport_id = a2.id group by route_id order by count(statistics.id) desc limit 10');

        $routes->addStringColumn('Top Routes')
            ->addNumberColumn('Searched times');

        foreach($response as $route){
            $route_name = $route->name1 . ' ' . $route->country1 . ' - ' . $route->name2 . ' ' . $route->country2 . ' (' . $route->departure_time . ' - ' . $route->arrival_time . ')';
            $routes->addRow([$route_name, $route->count]);
        }
        unset($route);

        Lava::BarChart('Most searched routes', $routes, []);

        $top_10_most_searched_routes = Lava::render('BarChart', 'Most searched routes', 'top-10-most-searched-routes-div', ['width' => "100%", 'height' => 300]);

        // get statistics

        $users_counter = DB::table('users')
            ->count();

        $airports_counter = DB::table('airports')
            ->count();

        $airlines_counter = DB::table('airlines')
            ->count();

        $routes_counter = DB::table('routes')
            ->count();

        $statistics = array(
            'users_counter' => $users_counter,
            'airports_counter' => $airports_counter,
            'airlines_counter' => $airlines_counter,
            'routes_counter' => $routes_counter,
        );

        // get routes grouped by searched timestamp

        $grouped_routes = Lava::DataTable();

        $routes = DB::select("select *, floor(time_to_sec(statistics.search_timestamp) / 3600) as 'from', ceil(time_to_sec(statistics.search_timestamp) / 3600) as 'to', count(id) as 'count' from statistics group by `from` order by `from` asc");

        $grouped_routes->addStringColumn('Time interval')
            ->addNumberColumn('Count');

        for($i = 0; $i < 23; $i++){
            $to = $i + 1;
            $found = false;
            foreach($routes as $route){
                if(intval($route->to) === $i){
                    $grouped_routes->addRow([$i.':00 - '. $to . ':00', $route->count]);
                    $found = true;
                }
            }
            unset($route);
            if(!$found) {
                $grouped_routes->addRow([$i . ':00 - ' . $to . ':00', 1]);
            }
        }

        Lava::ColumnChart('Searching time', $grouped_routes, []);

        $grouped_routes = Lava::render('ColumnChart', 'Searching time', 'searching-time', ['width' => "100%", 'height' => 700]);

        return view('statistics', [
            'top_10_most_searched_routes' => $top_10_most_searched_routes,
            'statistics' => $statistics,
            'grouped_routes' => $grouped_routes,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function profile(){
        $routes = DB::table('routes')
            ->join('airlines', 'routes.airline_id', '=', 'airlines.id')
            ->join('airports as s_a', 'routes.source_airport_id', '=', 's_a.id')
            ->join('airports as d_a', 'routes.destination_airport_id', '=', 'd_a.id')
            ->where('routes.added_by', Auth::user()->id)
            ->select([
                'airlines.name as airline',
                's_a.name as source',
                'd_a.name as destination',
                'routes.departure_time as departure',
                'routes.arrival_time as arrival',
                'routes.days',
                'routes.relaxed_note',
                'routes.stops'
            ])
            ->take(10)
            ->get();

        $preferences = DB::table('preferences')
            ->where('preferences.user_id', Auth::user()->id)
            ->orderBy('preferences.type', 'asc')
            ->select(
                'preferences.id as id',
                'preferences.type as type_id',
                'preferences.value1 as value1',
                'preferences.value2 as value2'
            )
            ->get();

        if(!empty($preferences)) {
            foreach ($preferences as &$preference) {
                if ($preference->type_id == 1) {
                    // destination airport
                    $preference->value1 = DB::table('airports')
                        ->where('airports.id', $preference->value1)
                        ->first();
                } else if ($preference->type_id == 2) {
                    // airline
                    $preference->value1 = DB::table('airlines')
                        ->where('airlines.id', $preference->value1)
                        ->first();
                } else if ($preference->type_id == 3) {
                    // source airport
                    // destination airport
                    $preference->value1 = DB::table('airports')
                        ->where('airports.id', $preference->value1)
                        ->first();
                    $preference->value2 = DB::table('airports')
                        ->where('airports.id', $preference->value2)
                        ->first();
                } else if ($preference->type_id == 4) {
                    // number of stops
                    // do nothing
                }
            }
            unset($preference);
        }else{
            $preferences = null;
        }

        return view('profile', ['user' => Auth::user(), 'routes' => $routes, 'preferences' => $preferences]);
    }

    /**
     * Returns an array with flights and a status code
     *
     * @param $source_airport_id - id of the source airport
     * @param $destination_airport_id - id of the destination airport
     * @param $airline_id - id of the airline that has the flight
     *
     * @return array - An array with flights
     */

    protected function getResults($source_airport_id, $destination_airport_id, $airline_id){
        $result = null;
        if(!empty($airline_id)) {
            $result = DB::table('routes')
                ->where('source_airport_id', $source_airport_id)
                ->where('destination_airport_id', $destination_airport_id)
                ->where('airline_id', $airline_id)
                ->get();
            if(!empty($result)) {
                return ['result' => $result, 'statusCode' => 1];
            }
        }else if(empty($airline_id)){
            $result = DB::table('routes')
                ->where('source_airport_id', $source_airport_id)
                ->where('destination_airport_id', $destination_airport_id)
                ->get();
            if(!empty($result)){
                return ['result' => $result, 'statusCode' => 1];
            }
        }

        $result = DB::table('routes')
            ->where('source_airport_id', $source_airport_id)
            ->where('destination_airport_id', $destination_airport_id)
            ->get();
        if(!empty($result)){
            return ['result' => $result, 'statusCode' => 2];
        }else{
            return ['result' => null, 'statusCode' => 3];
        }
    }

    /**
     *  Verify if there are flights for user's available days
     *
     * @param array $results - Array with flights
     * @param array $available_days - User's available days
     * @param int $statusCode - The status code from previous selection
     *
     * @return array $response - filtered results
     */

    protected function checkDate($results, $available_days, $statusCode){
        $response = array();
        foreach($results as $result){
            $flight_days = json_decode($result->days);
            foreach($available_days as $day){
                if(
                    in_array($day, $flight_days)
                ){
                    $response[$result->id] = $result;
                }
            }
            unset($day);
        }
        unset($result);

        if(!empty($response)) {
            $newStatusCode = $statusCode * 10 + 1;
            return ['result' => $response, 'statusCode' => $newStatusCode];
        }else{
            $newStatusCode = $statusCode * 10 + 2;
            return ['result' => $results, 'statusCode' => $newStatusCode];
        }
    }

    /**
     * @param $request - HTTP Request
     * @param $results - array of flights
     * @param $statusCode - status code from previous filter
     *
     * @return array $response - filtered array of flights
     */

    protected function matchRouteParams($request, $results, $statusCode){
        $response = array();

        // filter by night_flight
        if(
            $request->night_flight === 'true'
        ){
            foreach($results as $result){
                $source_airport = DB::table('airports')
                    ->where('id', $result->source_airport_id)
                    ->first();
                $destination_airport = DB::table('airports')
                    ->where('id', $result->destination_airport_id)
                    ->first();
                $sun_rise = date_sunrise(strtotime($result->arrival_time), SUNFUNCS_RET_TIMESTAMP, $destination_airport->latitude, $destination_airport->longitude, $destination_airport->timezone);
                $sun_set = date_sunset(strtotime($result->departure_time), SUNFUNCS_RET_TIMESTAMP, $source_airport->latitude, $source_airport->longitude, $source_airport->timezone);
                if(
                    strtotime($result->departure_time) > $sun_set &&
                    strtotime($result->departure_time) < $sun_rise &&
                    strtotime($result->arrival_time) < $sun_rise &&
                    strtotime($result->arrival_time) > $sun_set
                ){
                    $response[] = $result;
                }
            }
            unset($result);

            if(
                !empty($response)
            ){
                // night flight matches
                $newStatusCode = $statusCode * 10 + 1;
            }else{
                // no flights found
                $newStatusCode = $statusCode * 10 + 8;
                $response = $results;
            }
        } else if(
            $request->night_flight === 'false'
        ) {
            // night flight is optional
            $newStatusCode = $statusCode * 10 + 1;
            $response = $results;
        }

        $tmp_response = $response;
        unset($response);
        $response = array();

        // filter by relaxed_route

        if(
            $request->relaxed_route === 'true'
        ){
            if(
                $newStatusCode % 10 == 1
            ){
                // there are some responses
                foreach($tmp_response as $result){
                    if(
                        $result->relaxed_note == 5
                    ){
                        $response[] = $result;
                    }
                }
                unset($result);

                if(
                    !empty($response)
                ) {
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 1;
                }else{
                    // return the flights founded at previous step
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 5;
                    $response = $tmp_response;
                }
            }else if(
                $newStatusCode % 10 == 8
            ){
                // no night flights, search by relaxed route
                foreach($tmp_response as $result){
                    if(
                        $result->relaxed_note == 5
                    ){
                        $response[] = $result;
                    }
                }
                unset($result);

                if(
                    !empty($response)
                ) {
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 6;
                }else{
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 8;
                    $response = $tmp_response;
                }
            }
        }
        else if(
            $request->relaxed_route === 'false'
        ){
            // relaxed_route is optional
            if(
                $newStatusCode % 10 == 1
            ){
                $newStatusCode = intval($newStatusCode / 10);
                $newStatusCode = $newStatusCode * 10 + 2;
                $response = $tmp_response;
            }else
            if(
                $newStatusCode % 10 == 8
            ){
                $newStatusCode = intval($newStatusCode / 10);
                $newStatusCode = $newStatusCode * 10 + 6;
                $response = $tmp_response;
            }
        }

        $tmp_response = $response;
        unset($response);
        $response = array();

        // filter by stops

        if(
            $request->stops === 'true'
        ){
            // 1, 2, or 3 stops
            $stop_count = $request->stop_count;
            if($stop_count !== '0'){
                // specific number of stops
                foreach($tmp_response as $result){
                    if(
                        $result->stops == intval($stop_count)
                    ){
                        $response[] = $result;
                    }
                }
                unset($result);

                if(
                    !empty($response)
                ){
                    // possible statusCodes = 1, 2, 5, 6, 8

                    if(
                        $newStatusCode % 10 == 2
                    ){
                        $newStatusCode = intval($newStatusCode / 10);
                        $newStatusCode = $newStatusCode * 10 + 1;
                    }else if(
                        $newStatusCode % 10 == 5
                    ){
                        $newStatusCode = intval($newStatusCode / 10);
                        $newStatusCode = $newStatusCode * 10 + 3;
                    }else if(
                        $newStatusCode % 10 == 6
                    ){
                        $newStatusCode = intval($newStatusCode / 10);
                        $newStatusCode = $newStatusCode * 10 + 4;
                    }else if(
                        $newStatusCode % 10 == 8
                    ){
                        $newStatusCode = intval($newStatusCode / 10);
                        $newStatusCode = $newStatusCode * 10 + 7;
                    }
                }else{
                    // no results
                    // possible statusCodes = 1, 2, 5, 6, 8
                    // $response = $tmp_response;
                    if(
                        $newStatusCode % 10 == 1
                    ){
                        $newStatusCode = intval($newStatusCode / 10);
                        $newStatusCode = $newStatusCode * 10 + 2;
                    }
                    $response = $tmp_response;
                }
            }else if(
                $stop_count === '0'
            ){
                // any number of stops
                // possible statusCodes 1, 2, 5, 6, 8

                if(
                    $newStatusCode % 10 == 2
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 1;
                }else if(
                    $newStatusCode % 10 == 5
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 3;
                }else if(
                    $newStatusCode % 10 == 6
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 4;
                }else if(
                    $newStatusCode % 10 == 8
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 7;
                }
                $response = $tmp_response;
            }
        }
        else if(
            $request->stops === 'false'
        ){
            // with 0 stops

            foreach($tmp_response as $result){
                if(
                    $result->stops == 0
                ){
                    $response[] = $result;
                }
            }
            unset($result);

            //return array('result' => $response, 'statusCode' => $newStatusCode);

            if(
                !empty($response)
            ){
                // possible statusCodes = 1, 2, 5, 6, 8
                if(
                    $newStatusCode % 10 == 2
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 1;
                }else if(
                    $newStatusCode % 10 == 5
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 3;
                }else if(
                    $newStatusCode % 10 == 6
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 4;
                }else if(
                    $newStatusCode % 10 == 8
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 7;
                }
            }else{
                // no flights with 0 stops
                if(
                    $newStatusCode % 10 == 1
                ){
                    $newStatusCode = intval($newStatusCode / 10);
                    $newStatusCode = $newStatusCode * 10 + 2;
                }
                $response = $tmp_response;
            }
        }

        return ['result' => $response, 'statusCode' => $newStatusCode];
    }

    /**
     * Return hot offers added by administrator according to request params
     *
     * @param object $request - HTTP Request
     * @return array - flights with offers
     */

    protected function getHotOffers(){
        $offers = DB::select('select routes.*, offers.discount from offers join routes on offers.route_id = routes.id where start_time < NOW() and end_time > NOW() limit 0, 10 ');

        if(
            !empty($offers)
        ){
            return ['result' => $offers, 'statusCode' => 4];
        }else{
            return ['result' => null, 'statusCode' => 3];
        }
    }

    /**
     * Select flights from DB that are the mot searched by the users
     *
     * @param $request - HTTPRequest
     * @param $available_days - User's available days
     * @return array - Flights
     */

    protected function getMostSearchedFlights($request, $available_days){
        $flights = DB::select('select distinct routes.* from statistics join routes on routes.id = statistics.route_id group by route_id order by count(routes.id)');
        if(
            !empty($flights)
        ){
            $result = $this->checkDate($flights, $available_days, 1);
            if(
                $result['statusCode'] == '11' ||
                $result['statusCode'] == '21'
            ){
                // results found
                // filter by type of route (night or any, relaxed or any) and number of stops
                $result = $this->matchRouteParams($request, $result['result'], $result['statusCode']);

                if(
                    empty($result['result'])
                ){
                    $result = $this->getHotOffers();
                    return ['result' => $result['result'], 'statusCode' => $result['statusCode']];
                }
            }else{
                // no matches
                $result = $this->getHotOffers();
                return ['result' => $result['result'], 'statusCode' => $result['statusCode']];
            }
            $result = $this->getHotOffers();
            return ['result' => $result['result'], 'statusCode' => $result['statusCode']];
        }
        $result = $this->getHotOffers();
        return ['result' => $result['result'], 'statusCode' => $result['statusCode']];
    }

    protected function getWeather($time, $lat, $lon){
        $client = new Client(['headers' => ['X-CSRF-Token' => csrf_token()]]);
        $response = $client->request('GET', 'http://api.openweathermap.org/data/2.5/forecast/daily?lat=' . intval($lat) . '&lon=' . intval($lon) . '&cnt=10&mode=json&appid=30a1509ebfe4eb04e7c3d5db8ed97502');

        $data = json_decode((string)$response->getBody());
        $temps = $data->list;

        $temp = $temps[0]->temp->day - 273;
        $icon = $temps[0]->weather[0]->icon;
        $description = $temps[0]->weather[0]->main;

        $i = 0;
        while($temps[$i]->dt < $time){
            $temp = $temps[$i]->temp->day - 273;
            $icon = $temps[$i]->weather[0]->icon;
            $description = $temps[$i]->weather[0]->main;
            $i++;
        }

        return ['temperature' => $temp, 'icon' => $icon, 'description' => $description];
    }

    protected function computeHtmlResponse($result){
        $week_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $source_airport = DB::table('airports')
            ->where('id', $result->source_airport_id)
            ->first();

        $destination_airport = DB::table('airports')
            ->where('id', $result->destination_airport_id)
            ->first();

        $airline = DB::table('airlines')
            ->where('id', $result->airline_id)
            ->first();

        $response = '';

        $relaxed_note = '';
        for($i = 1; $i <= $result->relaxed_note; $i++){
            $relaxed_note .= '<i class="fa fa-star" aria-hidden="true"></i>';
        }

        $days = json_decode($result->days);
        foreach($days as $day) {
            if(strtotime($result->departure_time) >= time()) {
                $departure_date = date('Y-m-d', strtotime($week_days[$day]));
            }else{
                $departure_date = date('Y-m-d', strtotime('next ' . $week_days[$day]));
            }

            if(strtotime($result->arrival_time) >= strtotime($result->departure_time)) {
                $arrival_date = date('Y-m-d', strtotime($week_days[$day]));
            }else{
                $arrival_date = date('Y-m-d', strtotime($week_days[$day]) + 86400); // arrives next day
            }

            $source_airport_coordinates = DB::table('airports')
                ->where('id', $result->source_airport_id)
                ->first(['latitude', 'longitude']);

            $destination_airport_coordinates = DB::table('airports')
                ->where('id', $result->destination_airport_id)
                ->first(['latitude', 'longitude']);

            $departure_weather = $this->getWeather(strtotime($departure_date), $source_airport_coordinates->latitude, $source_airport_coordinates->longitude);
            $arrival_weather = $this->getWeather(strtotime($arrival_date), $destination_airport_coordinates->latitude, $destination_airport_coordinates->longitude);

            $response .= '<div class="result">
                <div class="departure">
                    <div class="weather" style="background: url(\'http://openweathermap.org/img/w/' . $departure_weather["icon"] . '.png\') no-repeat 50% 50%">
                        ' . $departure_weather["temperature"] . ' &#8451;
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <div style="text-align: center;"><strong style="text-align: center;">' . $departure_weather['description'] . '</strong></div>
                    </div>
                    <div class="data">
                        <h3><strong>Departure: ' . $source_airport->country . ', ' . $source_airport->name . ' (' . $source_airport->iata_faa . ')</strong></h3>
                        <h4>Departure time: ' . $departure_date . ', ' . $result->departure_time . '</h4>
                        <br/>
                        <h6>' . $result->stops . ' stops</h6>
                        ' . $relaxed_note . '
                    </div>
                </div>
                <div class="arrive">
                    <div class="weather" style="background: url(\'http://openweathermap.org/img/w/' . $arrival_weather["icon"] . '.png\') no-repeat 50% 50%">
                        ' . $arrival_weather{"temperature"} . ' &#8451;
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <div style="text-align: center;"><strong>' . $arrival_weather['description'] . '</strong></div>
                    </div>
                    <div class="data">
                        <h3><strong>Arrival: ' . $destination_airport->country . ', ' . $destination_airport->name . ' (' . $destination_airport->iata_faa . ')</strong></h3>
                        <h4>Arrival time: ' . $arrival_date . ', ' . $result->arrival_time . '</h4>
                        <br/>
                        <br/>
                        <div>with: ' . $airline->name . '</div>
                    </div>
                </div>
            </div>';
        }
        return $response;
    }

    protected function prepareHtmlResponse($result){
        if(
            !empty($result['result'])
        ){
            $response = '';
            // grab the right message for user according to statusCode
            if(
                $result['statusCode'] === 3 ||
                $result['statusCode'] === 4 ||
                $result['statusCode'] === 12 ||
                $result['statusCode'] === 22 ||
                $result['statusCode'] === 118 ||
                $result['statusCode'] === 218
            ) {
                $response = '<br/><br/>
                <div class="ui blue message">
                    <div class="header">
                        Sorry, we cannot found any routes for your request
                    </div>
                    <br/>
                    Instead, we\'ve prepared for you some <strong>hot offers</strong>
                </div>
                <h4 class="ui dividing header teal">Hot offers</h4>';
            }else if($result['statusCode'] === 111){
                $response = '<br/><br/><h4 class="ui dividing header teal">Results</h4>';
            }else if(
                $result['statusCode'] === 211 ||
                $result['statusCode'] === 112 ||
                $result['statusCode'] === 212 ||
                $result['statusCode'] === 113 ||
                $result['statusCode'] === 213 ||
                $result['statusCode'] === 114 ||
                $result['statusCode'] === 214 ||
                $result['statusCode'] === 115 ||
                $result['statusCode'] === 215 ||
                $result['statusCode'] === 116 ||
                $result['statusCode'] === 216 ||
                $result['statusCode'] === 117 ||
                $result['statusCode'] === 217
            ){
                $response = '<br/><br/>
                <div class="ui blue message">
                    <div class="header">
                    Sorry, we cannot found any routes for your request
                    </div>
                    <br/>
                    But, you can take a look at similar flights
                </div>';
                $response .= '<h4 class="ui dividing header teal">Suggestions</h4>';
            }

            foreach($result['result'] as $flight){
                $response .= $this->computeHtmlResponse($flight);
            }
            unset($result);
        }else{
            $response = 'Sorry, we have no suggestions for you!';
        }
        return $response;
    }

    /**
     * @param Request $request - The object that contains the HTTP Request information
     */

    public function searchForFlights(Request $request){
        $source_airport_id = $request->source;
        $destination_airport_id = $request->destination;
        $airline_id = $request->airline;
        $date = $request->date;
        $days = $request->days;

        if(
            empty($source_airport_id) ||
            empty($destination_airport_id)
        ){
            echo json_encode(['success' => 0]);
            return;
        }

        // result

        $week_days = ['mon' => 0, 'tue' => 1, 'wed' => 2, 'thu' => 3, 'fri' => 4, 'sat' => 5, 'sun' => 6];
        $available_days = array();

        if(empty($date)){
            // check the days
            foreach($days as $day){
                if($day['value'] == 'true'){
                    $available_days[] = $week_days[$day['name']];
                }
            }
            unset($day);

            // check if user's available days are empty
            if(
                empty($available_days)
            ){
                echo json_encode(['success' => 1]);
                return;
            }
        }else{
            $available_days[] = (date("w", strtotime($date)) + 6) % 7;
            foreach($days as $day){
                if($day['value'] == 'true'){
                    $available_days[] = $week_days[$day['name']];
                }
            }
            unset($day);
        }

        $available_days = array_unique($available_days);

        // status code = 1 => found
        // status code = 2 => found only for source and destination (not airline)
        // status code = 3 => not found
        $result = $this->getResults($source_airport_id, $destination_airport_id, $airline_id);

        if(
            ($result['statusCode'] == 1) ||
            ($result['statusCode'] == 2)
        ){
            // filter flights by date or days parameters
            $result = $this->checkDate($result['result'], $available_days, $result['statusCode']);

            if(
                $result['statusCode'] == '11' ||
                $result['statusCode'] == '21'
            ){
                // insert into statistics

                foreach((array)$result['result'] as $flight) {
                    if (
                    !DB::table('statistics')
                        ->insert([
                            'id' => null,
                            'user_id' => isset(Auth::user()->id) ? Auth::user()->id : null,
                            'route_id' => $flight->id,
                            'search_timestamp' => date('Y-m-d H:i:s', time())
                        ])
                    ) {
                        $this->logObj->error('Insert flight in statistics: Failed');
                    }
                }
                unset($flight);

                // results found
                // filter by type of route (night or any, relaxed or any) and number of stops
                $result = $this->matchRouteParams($request, $result['result'], $result['statusCode']);

                if(
                    empty($result['result'])
                ){
                    $result = $this->getMostSearchedFlights($request, $available_days);
                }else{
                    foreach((array)$result['result'] as $flight) {
                        if (
                            !DB::table('statistics')
                                ->insert([
                                    'id' => null,
                                    'user_id' => isset(Auth::user()->id) ? Auth::user()->id : null,
                                    'route_id' => $flight->id,
                                    'search_timestamp' => date('Y-m-d H:i:s', time())
                                ])
                        ) {
                            $this->logObj->error('Insert flight in statistics: Failed');
                        }
                    }
                    unset($flight);
                }
            }else{
                // no matches
                $result = $this->getMostSearchedFlights($request, $available_days);
            }
        }else if($result['statusCode'] == 3){
            // not found, call getMostSearchedFlights();
            $result = $this->getMostSearchedFlights($request, $available_days);
        }

        $result = $this->prepareHtmlResponse($result);

        echo json_encode($result);
    }

    /**
     * Echoes a list of airports according to the user's input
     */

    public function loadAirportsList(){
        $query = strip_tags($_POST['query']);
        if(strlen($query) == 3){
            $response = DB::table('airports')
                ->where('iata_faa', 'like', $query . '%')
                ->take(9)
                ->orderBy('iata_faa', 'desc')
                ->get();
        }else if(strlen($query) > 0 && strlen($query) != 3){
            $response = DB::table('airports')
                ->where('name', 'like', $query . '%')
                ->take(9)
                ->orderBy('name', 'desc')
                ->get();
        }

        if(isset($response)){
            echo json_encode($response);
        }else{
            echo json_encode(null);
        }
    }

    /**
     * Get the full list of all airlines
     */

    public function loadAllAirlinesList(){
        $airlines = DB::table('airlines')
            ->orderBy('name', 'asc')
            ->get();

        return !empty($airlines) ? json_encode(['response' => $airlines]) : json_encode(['response' => '0']);
    }

    /**
     * Get the full list of all airports
     */

    public function loadAllAirportsList(){
        $airports = DB::table('airports')
            ->orderBy('country', 'asc')
            ->get();

        return !empty($airports) ? json_encode(['response' => $airports]) : json_encode(['response' => '0']);
    }

    /**
     * Removes user's selected preference
     */

    public function deletePreference(){
        $preference_id = strip_tags($_POST['preference_id']);
        if(
            !DB::table('preferences')
                ->where('id', $preference_id)
                ->delete()
        ){
            echo 1;
        }else{
            echo 0;
        }
    }

    /**
     * Adds user's preferences
     */

    public function addPreferences(){
        $preferences = json_decode(strip_tags(json_encode($_POST['preferences'])));
        $response = null;
        if(!empty($preferences)) {
            foreach ($preferences as $preference) {
                if ($preference->type == 'airport') {
                    $id = DB::table('preferences')
                        ->insertGetId([
                            'user_id' => Auth::user()->id,
                            'type' => 1,
                            'value1' => $preference->value1,
                            'value2' => null
                        ]);
                    if(
                        !isset($id)
                    ){
                        echo 1;
                        return;
                    }else{
                        // append new preference to response
                        $airport_data = DB::table('airports')
                            ->where('id', $preference->value1)
                            ->first();
                        $airport = ['type_id' => 1, 'id' => $id, 'value1' => $airport_data];
                        $response[] = $airport;
                    }
                } else if($preference->type == 'airline') {
                    $id = DB::table('preferences')
                        ->insertGetId([
                            'id' => null,
                            'user_id' => Auth::user()->id,
                            'type' => 2,
                            'value1' => $preference->value1,
                            'value2' => null
                        ]);
                    if(
                        !isset($id)
                    ){
                        echo 1;
                        return;
                    }else{
                        // append new preference to response
                        $airline_data = DB::table('airlines')
                            ->where('id', $preference->value1)
                            ->first();
                        $airline = ['type_id' => 2, 'id' => $id, 'value1' => $airline_data];
                        $response[] = $airline;
                    }
                } else if($preference->type == 'route') {
                    $id = DB::table('preferences')
                        ->insertGetId([
                            'id' => null,
                            'user_id' => Auth::user()->id,
                            'type' => 3,
                            'value1' => $preference->value1,
                            'value2' => $preference->value2
                        ]);
                    if(
                        !isset($id)
                    ){
                        echo 1;
                        return;
                    }else{
                        // append new preference to response
                        $source_airport_data = DB::table('airports')
                            ->where('id', $preference->value1)
                            ->first();
                        $destination_airport_data = DB::table('airports')
                            ->where('id', $preference->value2)
                            ->first();
                        $route = ['type_id' => 3, 'id' => $id, 'value1' => $source_airport_data, 'value2' => $destination_airport_data];
                        $response[] = $route;
                    }
                } else if($preference->type == 'stop') {
                    $id = DB::table('preferences')
                        ->insertGetId([
                            'id' => null,
                            'user_id' => Auth::user()->id,
                            'type' => 4,
                            'value1' => $preference->value1,
                            'value2' => null
                        ]);
                    if(
                        !isset($id)
                    ){
                        echo 1;
                        return;
                    }else{
                        // append new preference to response
                        $stop = ['type_id' => 4, 'id' => $id, 'value1' => $preference->value1];
                        $response[] = $stop;
                    }
                }
            }
            unset($preference);
        }

        if(!empty($preferences)){
            echo json_encode($response);
        }else{
            echo 1;
            return;
        }
        return;
    }

    /**
     * Updates user's information (name and password)
     */

    public function updatePersonalInformation(){
        $data = null;
        $data = json_decode(strip_tags(json_encode($_POST['data'])));
        $query_params = null;

        $user = Auth::user();

        if(
            isset($data->name)
        ){
            if($user->name !== $data->name) {
                $query_params['name'] = $data->name;
            }
        }

        if(
            isset($data->password) &&
            isset($data->confirm_password)
        ){
            if(
                $data->password === $data->confirm_password &&
                $data->password !== $user->password
            ){
                $query_params['password'] = bcrypt($data->password);
            }else{
                echo 0;
                return;
            }
        }

        if(
            $data->latitude < 180 &&
            $data->latitude > -180 &&
            $data->longitude < 180 &&
            $data->longitude > -180
        ){
            $query_params['latitude'] = $data->latitude;
            $query_params['longitude'] = $data->longitude;
        }

        if(
            isset($query_params)
        ){
            if(
                !DB::table('users')
                    ->where('id', Auth::user()->id)
                    ->update($query_params)
            ){
                echo 0;
                return;
            }else{
                echo 1;
                return;
            }
        }else{
            echo 0;
            return;
        }
    }

    /**
     * Return a status message that corresponds to user's existence and linking between fb and the app
     */

    public function getUserByFBId(){
        $data = json_decode(strip_tags(json_encode($_POST['response'])));
        $user_data = json_decode(strip_tags(json_encode($_POST['user_data'])));
        if($data->status === 'connected'){
            $user = User::where('fb_user_id', 'like', $data->authResponse->userID)
                ->first();

            if(
                !empty($user)
            ){
                Auth::login($user);
                echo json_encode(['response' => 'connected']);
                return;
            }else{
                if(
                    isset($user_data->email) &&
                    isset($user_data->name) &&
                    isset($user_data->id)
                ){
                    $user_id =DB::table('users')
                        ->insertGetId(
                            [
                                'id' => null,
                                'name' => $user_data->name,
                                'password' => null,
                                'email' => $user_data->email,
                                'role_id' => 1,
                                'remember_token' => null,
                                'fb_user_id' => $user_data->id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    if(
                        !$user_id
                    ){
                        echo json_encode(['response' => 'not_linked']);
                        return;
                    }else{
                        $user = User::find($user_id);
                        Auth::login($user);
                        echo json_encode(['response' => 'connected']);
                        return;
                    }
                }else{
                    echo json_encode(['response' => 'not_linked']);
                    return;
                }
            }
        }else{
            echo json_encode(['response' => 'disconnected']);
            return;
        }
    }

    public function linkWithFacebook(){
        $response = json_decode(strip_tags(json_encode($_POST['response'])));
        if($response){
            $profile_is_linked = DB::table('users')
                ->where('fb_user_id', $response->id)
                ->count();
            if($profile_is_linked > 0){
                echo json_encode(["response" => 2]);
                return;
            }
            if(Auth::user()->fb_user_id != $response->id){
                if(
                    !DB::table('users')
                        ->where('id', Auth::user()->id)
                        ->update(['fb_user_id' => $response->id])
                ){
                    echo json_encode(["response" => 0]);
                    return;
                }else{
                    echo json_encode(["response" => 1]);
                    return;
                }
            }
        }
    }

    public function getUserLocation(){
        $user = Auth::user();
        $latitude = intval($user->latitude) == 0 ? 0 : $user->latitude;
        $longitude = intval($user->longitude) == 0 ? 0 : $user->longitude;

        return json_encode(['latitude' => $latitude, 'longitude' => $longitude]);
    }

    /**
     * @param $lat - User's location latitude
     * @param $lon - User's location longitude
     *
     * @return Most searched routes based on user's location
     */

    public function getMostSearchedRoutesByLocation($lat, $lon){
        // select the nearest 3 airports by distance
        $airports = DB::select('select airports.*, pow((airports.latitude - ?),2) + pow((airports.longitude - ?),2) as distance from `airports` order by distance asc limit 3', [$lat, $lon]);
        $airport_ids = [];
        foreach($airports as $airport){
            $airport_ids[] = $airport->id;
        }
        $airport_ids = '(' . implode(', ', $airport_ids) . ')';
        $results = [];
        if(!empty($airport_ids)) {
            // get top 12 searched routes sorted by nearest airports
            $results = DB::select('select *, count(statistics.route_id) as `count` from routes join statistics on routes.id = statistics.route_id where routes.source_airport_id in ' . $airport_ids . ' group by statistics.route_id union select *, 0 as `count` from routes left join statistics on routes.id = statistics.route_id where statistics.route_id is null and routes.source_airport_id in ' . $airport_ids . ' order by `count` desc limit 12');
        }

        if(!empty($results)){
            foreach($results as &$result){
                // get source airport details
                $result->source_airport_id = DB::table('airports')
                    ->where('id', $result->source_airport_id)
                    ->first();

                $result->source_weather = $this->getWeather(time(), intval($result->source_airport_id->latitude), intval($result->source_airport_id->latitude));

                // get destination airport details
                $result->destination_airport_id = DB::table('airports')
                    ->where('id', $result->destination_airport_id)
                    ->first();

                $result->destination_weather = $this->getWeather(time(), intval($result->destination_airport_id->latitude), intval($result->destination_airport_id->latitude));

                // get airline details
                $result->airline_id = DB::table('airlines')
                    ->where('id', $result->airline_id)
                    ->first();
            }
        }

        return $results;
    }
}