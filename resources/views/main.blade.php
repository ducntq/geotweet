@extends('layout')
@section('content')
    <div class="container-fluid" id="viewport">
        <div class="row">
            <div id="map"></div>
        </div>
        <div class="row no-gutters" id="controls">
            <div class="col-md-8">
                <input type="text" class="form-control input-lg" placeholder="Enter a city to begin" />
            </div>
            <div class="col-md-2"><button class="btn btn-primary btn-block btn-lg">Search</button></div>
            <div class="col-md-2"><button class="btn btn-warning btn-block btn-lg">History</button></div>
        </div>
    </div>
@endsection