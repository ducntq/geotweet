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
        var_dump($city);die();

        return response()->json($query);
    }
}
