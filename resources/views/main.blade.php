@extends('layout')
@section('content')
    <div class="container-fluid" id="viewport">
        <div class="row">
            <div id="map"></div>
        </div>
        <div class="row no-gutters" id="controls">
            <div class="col-md-8 col-sm-12 col-xs-12">
                <input type="text" class="form-control input-lg" placeholder="Enter a city to begin" />
            </div>
            <div class="col-md-2 col-sm-6 col-xs-6"><button class="btn btn-primary btn-block btn-lg">Search</button></div>
            <div class="col-md-2 col-sm-6 col-xs-6">
                <div class="btn-group dropup btn-block">
                    <button type="button" class="btn btn-default dropdown-toggle btn-lg btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        History <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Empty</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection