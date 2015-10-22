var map;
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 12.19070471984058, lng: 94.394},
        zoom: 3
    });

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

    /**
     * Bind click event on Search button
     */
    function bindSearchBtn() {
        $searchBtn.off().on('click', function(e) {
            e.preventDefault();
        });
    }

    function search(query, successCallback, failureCallback) {

    }

    /**
     * Expose public `bind` method
     */
    GeoTweet.prototype.bind = function () {
        bindSearchBtn();
    };

    return GeoTweet;
})();