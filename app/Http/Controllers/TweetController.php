<?php
/**
 * TweetController for requesting Tweets
 *
 * User: ducntq
 * Date: 22/10/2015
 * Time: 20:00
 */

namespace App\Http\Controllers;


use App\City;
use App\Tweet;
use Carbon\Carbon;

class TweetController extends Controller
{
    /**
     * Display list of tweets by query
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = ['data' => [], 'errors' => []];
        $query = \Request::get('place', '');

        // if query is empty, return empty list
        if (empty($query)) return response()->json($result);

        $city = City::findWithPlaceId($query);

        $tweets = Tweet::fetchByCity($city, '50km', 50);
        if (!empty($tweets)) {
            $result['data'] = ['city' => $city, 'tweets' => $tweets];
        }

        return response()->json($result);
    }
}
