<?php
/**
 * Main controller, will be served as homepage
 *
 * User: ducntq
 * Date: 21/10/2015
 * Time: 23:00
 */

namespace App\Http\Controllers;


use App\History;

class MainController extends Controller
{
    public function index()
    {
        $recentSearches = [];

        // check for identifier cookie
        $cookieIdName = 'id';
        $userId = \Cookie::get($cookieIdName);
        if ($userId) {
            // if userid is found in cookie, then proceed to fetch history from database
            $recentSearches = History::whereUserId($userId)->orderBy('created_at', 'desc')->get();
        } else {
            // if userid is not found, generate new cookie, expires in 7 days
            \Cookie::queue($cookieIdName, str_random(32), (7 * 24 * 60));
        }

        return view('main', ['recentSearches' => $recentSearches]);
    }
}