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
    var $, map, $input, $searchBtn, $historyBtn, $history, $historyList, $closeHistory, markers = [];

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
        $history = $('#history-container');
        $historyList = $history.find('> ul');
        $closeHistory = $('#close-history');
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
            $input.popover('hide');
            if (e.keyCode == 13) {
                e.preventDefault();
                performSearch(getQuery());
            }
        });
    }

    /**
     * Bind click event on history button
     */
    function bindHistoryBtn() {
        $historyBtn.on('click', function(e) {
            e.preventDefault();
            $history.toggleClass('show');
        });
    }

    /**
     * Bind click event on `Back to tweets` link
     */
    function bindCloseHistoryBtn() {
        $closeHistory.on('click', function(e) {
            e.preventDefault();
            $history.removeClass('show');
        });
    }

    function bindHistoryItemClick() {
        $historyList.on('click', '.history-item a', function(e) {
            var $this = $(this), city = $this.attr('data-city');
            e.preventDefault();
            $input.val(city);
            performSearch(city);
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
                createHistoryItem(city);

                for (var i = 0; i < totalTweets; i++) {
                    drawTweet(tweets[i]);
                }
            }, function (errors) {
                if (errors.length > 0) {
                    $input.attr('data-content', errors[0]).popover('show');

                    setTimeout(function() {
                        $input.popover('hide');
                    }, 5000);
                } else {
                    alert("Something went wrong");
                }
            });
        }
    }

    /**
     * Create history item, prepend to history list
     * @param city
     */
    function createHistoryItem(city) {
        var $li = $('<li />').addClass('history-item');
        var $history = $('<a />').attr('href', '#').attr('data-city', city.name).text(city.name);
        $history.appendTo($li);
        $li.insertAfter($closeHistory);
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
        bindHistoryBtn();
        bindCloseHistoryBtn();
        bindHistoryItemClick();
    };

    GeoTweet.prototype.focusInput = function() {
        focusOnInput();
    };

    return GeoTweet;
})();