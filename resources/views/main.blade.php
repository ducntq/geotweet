@extends('layout')
@section('content')
    <div class="container-fluid" id="viewport">
        <div class="row">
            <div id="map"></div>
        </div>
        <div class="row no-gutters" id="controls">
            <div class="col-md-8 col-sm-12 col-xs-12">
                <input type="text" id="txtInput" class="form-control input-lg" placeholder="Enter a city name to begin" data-container="body" data-toggle="popover" data-placement="top" data-trigger="manual" data-content="" />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-6">
                <button id="search-btn" class="btn btn-primary btn-block btn-lg">Search</button>
            </div>
            <div class="col-md-2 col-sm-6 col-xs-6">
                <button id="history-btn" class="btn btn-default btn-block btn-lg">History</button>
            </div>
        </div>
        <div id="title">Welcome to GeoTweet</div>
        <div id="history-container">
            <ul>
                <li id="close-history"><a href="#">&lt; Back to tweets</a></li>
                @foreach ($recentSearches as $item)
                <li class="history-item">
                    <a data-city="{{ $item->city_name }}" href="#">{{ $item->city_name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection