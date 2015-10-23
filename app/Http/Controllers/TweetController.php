<?php

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
        $query = \Request::get('query', '');

        // if query is empty, return empty list
        if (empty($query)) return response()->json($result);

        $city = City::findWithQuery($query);

        if ($city) {
            $tweets = Tweet::fetchByCity($city, '50km', 30);
            if (!empty($tweets)) {
                $result['data'] = [ 'city' => $city, 'tweets' => $tweets];
            }
        }

        return response()->json($result);
    }
}
