<?php
/**
 * Main controller, will be served as homepage
 *
 * User: ducntq
 * Date: 21/10/2015
 * Time: 23:00
 */

namespace App\Http\Controllers;


class MainController extends Controller
{
    public function index()
    {
        return view('main');
    }
}