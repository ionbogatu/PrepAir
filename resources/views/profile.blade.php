<?php
/**
 * Created by PhpStorm.
 * User: John Rich
 * Date: 4/25/2016
 * Time: 12:59 PM
 */
?>

@extends ('layouts.layout')
@section ('content')
    <br/>
    <br/>
    <div class="ui container">
        <div class="ui form">
            <h4 class="ui dividing header">Personal information</h4>
            <div class="field">
                <div class="three fields">
                    <div class="field">
                        <label>Name</label>
                        <input name="name" placeholder="{{ $user->name }}" type="text">
                    </div>
                </div>
                <br/>
                <div class="three fields">
                    <div class="field">
                        <label>Password</label>
                        <input name="password" value="password" type="password">
                    </div>
                    <div class="field">
                        <label>Confirm password</label>
                        <input name="password" placeholder="Confirm password" type="password">
                    </div>
                </div>
                <br/>
                <div class="three fields">
                    <div class="field">
                        <input name="submit" class="ui teal button" value="Update profile" type="submit">
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <br/>
        <h4 class="ui dividing header">Added Routes</h4>
        <a href="#" class="add-new add-route">+Add new</a>
        <table class="ui selectable inverted table">
            <thead>
            <tr>
                <th>N/o</th>
                <th>Airline</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Departure time</th>
                <th>Arrival time</th>
                <th>Flight days</th>
                <th>Stops</th>
                <th>Note</th>
                <th class="right aligned">Actions</th>
            </tr>
            </thead>
            <tbody>
            @if (empty($routes))
                <tr>
                    <td colspan="10" style="text-align: center;">You don't have any custom routes</td>
                </tr>
            @else
                <?php $i = 1; ?>
                @foreach($routes as $route)
                    <?php
                        $pretty_printed_days = '';
                        $days = json_decode($route->days);
                        foreach ($days as $day){
                            switch($day){
                                case 0: $pretty_printed_days .= 'Mon, ';
                                    break;
                                case 1: $pretty_printed_days .= 'Tue, ';
                                    break;
                                case 2: $pretty_printed_days .= 'Wed, ';
                                    break;
                                case 3: $pretty_printed_days .= 'Thu, ';
                                    break;
                                case 4: $pretty_printed_days .= 'Fri, ';
                                    break;
                                case 5: $pretty_printed_days .= 'Sat, ';
                                    break;
                                case 6: $pretty_printed_days .= 'Sun, ';
                                    break;
                            }
                        }
                        $pretty_printed_days = rtrim($pretty_printed_days, ', ');
                    ?>
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $route->airline }}</td>
                        <td>{{ $route->source }}</td>
                        <td>{{ $route->destination }}</td>
                        <td>{{ $route->departure }}</td>
                        <td>{{ $route->arrival }}</td>
                        <td>{{ $pretty_printed_days }}</td>
                        <td>{{ $route->stops }}</td>
                        <td>{{ $route->relaxed_note }}</td>
                        <td class="right aligned">
                            <a href="#">Change</a>
                            <span>|</span>
                            <a href="#">Delete</a>
                        </td>
                    </tr>
                @endforeach
            @endif
            <!--tr>
                <td>John</td>
                <td>Approved</td>
                <td class="right aligned">None</td>
            </tr>
            <tr>
                <td>Jamie</td>
                <td>Approved</td>
                <td class="right aligned">Requires call</td>
            </tr>
            <tr>
                <td>Jill</td>
                <td>Denied</td>
                <td class="right aligned">None</td>
            </tr-->
            </tbody>
        </table>
        <br/>
        <br/>
        <h4 class="ui dividing header">Added Airports</h4>
        <p class="add-new add-airport">+Add new</p>
        <table class="ui selectable inverted table">
            <thead>
            <tr>
                <th>N/o</th>
                <th>Status</th>
                <th class="right aligned">Notes</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>John</td>
                <td>Approved</td>
                <td class="right aligned">None</td>
            </tr>
            <tr>
                <td>Jamie</td>
                <td>Approved</td>
                <td class="right aligned">Requires call</td>
            </tr>
            <tr>
                <td>Jill</td>
                <td>Denied</td>
                <td class="right aligned">None</td>
            </tr>
            </tbody>
        </table>
        <br/>
        <br/>
        <h4 class="ui dividing header">Added Airlines</h4>
        <p class="add-new add-airline">+Add new</p>
        <table class="ui selectable inverted table">
            <thead>
            <tr>
                <th>N/o</th>
                <th>Status</th>
                <th class="right aligned">Notes</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>John</td>
                <td>Approved</td>
                <td class="right aligned">None</td>
            </tr>
            <tr>
                <td>Jamie</td>
                <td>Approved</td>
                <td class="right aligned">Requires call</td>
            </tr>
            <tr>
                <td>Jill</td>
                <td>Denied</td>
                <td class="right aligned">None</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection