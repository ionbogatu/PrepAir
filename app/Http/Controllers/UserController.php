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
use App\User;

class UserController extends Controller{

    public function flights(){
        $airlines = DB::table('airlines')
            ->orderBy('name')
            ->get();
        return view('flights', ['airlines' => $airlines]);
    }

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
        return view('profile', ['user' => Auth::user(), 'routes' => $routes]);
    }

    public function searchForFlights(Request $request){
        $this->validate($request, [
            'source' => 'required',
            'destination' => 'required'
        ]);

        echo json_encode($_POST);
    }

    public function loadAirportsList(){
        $query = $_POST['query'];
        if(strlen($query) == 3){
            $response = DB::table('airports')
                ->where('iata_faa', 'like', strtoupper($query) . '%')
                ->take(9)
                ->orderBy('iata_faa', 'desc')
                ->get();
        }else if(strlen($query) > 0 && strlen($query) != 3){
            $response = DB::table('airports')
                ->where('name', 'like', ucfirst(strtolower($query)) . '%')
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
}