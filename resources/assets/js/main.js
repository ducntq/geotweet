var map;
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 12.19070471984058, lng: 94.394},
        zoom: 3
    });
    // init new GeoTweet instance and bind events
    var geoTweet = new GeoTweet(jQuery, map);
    geoTweet.bind();
    geoTweet.focusInput();
}

var GeoTweet = (function () {
    var $, map, $input, $searchBtn, $historyBtn, markers = [];

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

    /**
     * Get query from input
     * @returns {*}
     */
    function getQuery() {
        return $input.val();
    }

    function focusOnInput() {
        $input.focus();
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
        clearMarkers();
        if (query.length > 0) {
            markers = [];
            search(query, function(data) {
                var city = data.city, tweets = data.tweets, totalTweets = tweets.length;
                mapPanAndZoom(city, 13);

                for (var i = 0; i < totalTweets; i++) {
                    drawTweet(tweets[i]);
                }
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
     * Draw a tweet on the map
     * @param tweet
     */
    function drawTweet(tweet) {
        var image = tweet.user_avatar;
        var coords = {lat: parseFloat(tweet.latitude), lng: parseFloat(tweet.longitude)};

        // info box
        var $contentContainer = $('<div />').addClass('user-tweet');
        $('<div />').addClass('content').text(tweet.content).appendTo($contentContainer);
        $('<div />').addClass('meta').text('When: ' + tweet.fetched_at).appendTo($contentContainer);
        var content = $contentContainer[0].outerHTML;
        var info = new google.maps.InfoWindow({
            content: content
        });

        var marker = new google.maps.Marker({
            position: coords,
            map: map,
            icon: {
                url: image,
                size:new google.maps.Size(48,48)},
                shape: {
                    coords:[17,17,18],
                    type:'circle'
                },
            optimized:false,
            title: tweet.content
        });
        markers.push(marker);

        marker.addListener('click', function() {
            info.open(map, marker);
        });
    }

    /**
     * Remove all markers on map
     */
    function clearMarkers() {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
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

    GeoTweet.prototype.focusInput = function() {
        focusOnInput();
    };

    return GeoTweet;
})();