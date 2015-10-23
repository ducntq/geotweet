<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\City
 * 
 * Class City represents a city as a location
 * with longitude and latitude
 *
 * @property string $id Unique identifier of city
 * @property string $name Name of city
 * @property string $latitude Latitude of city
 * @property string $longitude Longitude of city
 * @property string $country Full country name of city
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $fetched_at
 * @method static \Illuminate\Database\Query\Builder|\App\City whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\City whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\City whereLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\City whereLongitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\City whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\City whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\City whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\City whereFetchedAt($value)
 * @property string $place_id
 * @method static \Illuminate\Database\Query\Builder|\App\City wherePlaceId($value)
 */
class City extends Model
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'city';

    /**
     * List of properties will be transformed to `Carbon` object
     * @var array
     */
    protected $dates = ['fetched_at'];

    /**
     * Find City by `placeId`, create a new City if nothing is found
     *
     * @param $placeId
     * @return City|array|mixed|static
     */
    public static function findWithPlaceId($placeId)
    {
        $result = City::wherePlaceId($placeId)->first();

        // return result when found in database
        if ($result) return $result;
        else {
            $response = json_decode(\GoogleMaps::load('placedetails')->setParam(['placeid' => $placeId])->get());
            if (empty($response->result) || empty($response->status) || $response->status != 'OK') return [];
            $city = City::loadFromPlace($response->result);
            $city->save();
            return $city;
        }
    }

    /**
     * Generate an instance of City with raw data from Google Places
     *
     * @param $place
     * @return City
     */
    public static function loadFromPlace($place)
    {
        $city = new City();
        $city->name = $place->name;
        $city->place_id = (string)$place->place_id;
        $city->latitude = $place->geometry->location->lat;
        $city->longitude = $place->geometry->location->lng;
        return $city;
    }
}
