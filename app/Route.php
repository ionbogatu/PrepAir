<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 4/23/2016
 * Time: 9:18 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model{

    public $timestamps = false;

    protected $fillable = ['id', 'airline_id', 'source_airport_id', 'destination_airport_id', 'stops'];

}