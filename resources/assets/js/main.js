var map;
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 12.19070471984058, lng: 94.394},
        zoom: 3
    });
    console.log('Map initialized');
    // init new GeoTweet instance and bind events
    var geoTweet = new GeoTweet(jQuery, map);
    geoTweet.bind();
}

var GeoTweet = (function () {
    var $, map, $input, $searchBtn, $historyBtn;

    /**
     * Constructor of GeoTweet
     *
     * Init all jQuery objects
     *
     * @param jQuery
     * @param googleMap
     * @constructor
     */
    function GeoTweet (jQuery, googleMap) {
        $ = jQuery;
        map = googleMap;

        $input = $('#txtInput');
        $searchBtn = $('#search-btn');
        $historyBtn = $('#history-btn');
    }

    function getQuery() {
        return $input.val();
    }

    /**
     * Bind click event on Search button
     */
    function bindSearchBtn() {
        $searchBtn.off().on('click', function(e) {
            e.preventDefault();
            performSearch(getQuery());
        });
    }

    /**
     * Bind keydown event on query input, capture for Return
     */
    function bindQueryInputKeyup() {
        $input.on('keyup', function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                performSearch(getQuery());
            }
        });
    }

    /**
     * Perform a search with query. On success, zoom to city location
     * and display tweets on map
     * @param query
     */
    function performSearch(query) {
        if (query.length > 0) {
            search(query, function(data) {
                var city = data.city, tweets = data.tweets;
                mapPanAndZoom(city, 13);
            });
        }
    }

    /**
     * Center map to a city and zoom
     * @param city
     * @param zoom
     */
    function mapPanAndZoom(city, zoom) {
        var coords = {lat: parseFloat(city.latitude), lng: parseFloat(city.longitude)};
        map.panTo(coords);
        if (typeof zoom == 'number') map.setZoom(zoom);
    }

    /**
     * Perform a search with query
     * @param query
     * @param successCallback
     * @param failureCallback
     */
    function search(query, successCallback, failureCallback) {
        var apiUrl = '/tweet';
        $.getJSON(apiUrl, {query: encodeURIComponent(query)}, function(response) {
            if (response && response.errors.length == 0) {
                // if success
                if (typeof successCallback == 'function') successCallback(response.data);
            } else {
                // if something goes wrong
                if (typeof failureCallback == 'function') failureCallback(response.errors);
            }
        });
    }

    /**
     * Expose public `bind` method
     */
    GeoTweet.prototype.bind = function () {
        bindSearchBtn();
        bindQueryInputKeyup();
    };

    return GeoTweet;
})();