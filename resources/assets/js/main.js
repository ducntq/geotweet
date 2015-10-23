var map, geoTweet, places, autocomplete;
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 12.19070471984058, lng: 94.394},
        zoom: 3
    });

    // init new GeoTweet instance and bind events
    geoTweet = new GeoTweet(jQuery, map);
    geoTweet.bind();
    geoTweet.focusInput();
    geoTweet.alignTitle();
}

var GeoTweet = (function () {
    var $, map, $input, $searchBtn, $historyBtn, $history, $historyList, $closeHistory,
        $title, markers = [], places, autocomplete, infoBoxes;

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
        $title = $('#title');

        initAutocomplete();
    }

    /**
     * Initialize Google Places Autocomplete
     */
    function initAutocomplete() {
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */ (
                document.getElementById('txtInput')), {
                types: ['(cities)']
            });
        places = new google.maps.places.PlacesService(map);
    }

    /**
     * Handle event when place is changed
     */
    function bindPlaceChanged() {
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (place.geometry) {
                map.panTo(place.geometry.location);
                map.setZoom(15);
                performSearch(place.place_id);
            }
        });
    }

    /**
     * Set focus to text box
     */
    function focusOnInput() {
        $input.focus();
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

    /**
     * Bind click event on history items
     */
    function bindHistoryItemClick() {
        $historyList.on('click', '.history-item a', function(e) {
            var $this = $(this), placeId = $this.attr('data-city');
            e.preventDefault();

            performSearch(placeId);
            /*
            var request = {placeId: placeId};
            var service = new google.maps.places.PlacesService(map);
            service.getDetails(request, function (place, status) {
                if (status == google.maps.places.PlacesServiceStatus.OK) {
                    map.panTo(place.geometry.location);
                    map.setZoom(15);
                }
            });
            */
        });
    }

    /**
     * Hide title
     */
    function hideTitle() {
        $title.css('z-index', -1);
    }

    /**
     * Align title to center of screen
     */
    function centerTitle() {
        hideTitle();
        var windowWidth = $(window).width();
        var titleWidth = $title.width();
        var left = (windowWidth - titleWidth) / 2;
        if (left < 0) left = 0;
        $title.css('left', left).css('z-index', 10);
    }

    /**
     * Set content of title
     * @param text
     */
    function setTitle(text) {
        $title.text(text);
    }

    /**
     * Perform a search with query. On success, zoom to city location
     * and display tweets on map
     * @param place
     */
    function performSearch(place) {
        clearMarkers();
        hideTitle();
        if (place.length > 0) {
            markers = [];
            search(place, function(data) {
                var city = data.city, tweets = data.tweets, totalTweets = tweets.length;
                mapPanAndZoom(city, 13);
                createHistoryItem(city);
                setTitle('Tweets about ' + city.name);
                centerTitle();

                infoBoxes = [];
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
        infoBoxes.push(info);

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
            for(var i = 0; i < infoBoxes.length; i++) {
                infoBoxes[i].close();
            }
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
     * @param place
     * @param successCallback
     * @param failureCallback
     */
    function search(place, successCallback, failureCallback) {
        var apiUrl = '/tweet';
        var data = {};
        if (typeof place == 'object') data = {place: encodeURIComponent(place.place_id)};
        else data = {place: encodeURIComponent(place)};
        $.getJSON(apiUrl, data, function(response) {
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
        bindHistoryBtn();
        bindCloseHistoryBtn();
        bindHistoryItemClick();
        bindPlaceChanged();
    };

    /**
     * Expose focusInput
     */
    GeoTweet.prototype.focusInput = function() {
        focusOnInput();
    };

    /**
     * Expose alignTitle
     */
    GeoTweet.prototype.alignTitle = function() {
        centerTitle();
    };

    return GeoTweet;
})();