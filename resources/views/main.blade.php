@extends('layout')
@section('content')
    <div class="container-fluid" id="viewport">
        <div class="row no-gutters" id="controls">
            <div class="col-md-10 col-sm-9 col-xs-9">
                <input type="text" id="txtInput" class="form-control input-lg" placeholder="Enter a city name to begin" data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="manual" data-content="" />
            </div>
            <div class="col-md-2 col-sm-3 col-xs-3">
                <button id="history-btn" class="btn btn-primary btn-block btn-lg">History</button>
            </div>
        </div>
        <div class="row" id="map-row">
            <div id="map"></div>
        </div>
        <div id="title">Welcome to GeoTweet</div>
        <div id="history-container">
            <ul>
                <li id="close-history"><a href="#">&lt; Back to tweets</a></li>
                @foreach ($recentSearches as $item)
                <li class="history-item">
                    <a data-city="{{ $item->place_id }}" href="#">{{ $item->city_name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection