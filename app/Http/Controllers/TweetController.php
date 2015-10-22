<?php

namespace App\Http\Controllers;


use App\City;

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
            $twitterQuery = [
                'q' => '',
                'geocode' => $city->latitude . ',' . $city->longitude . ',10km',
                'count' => 10
            ];
            $tweets = \Twitter::getSearch($twitterQuery);
            echo '<pre>';
            print_r($tweets->statuses[0]);
            echo '</pre>';
            die();
        }

        return response()->json($query);
    }
}
