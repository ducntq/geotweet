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
     * @param $query string Query for searching
     * @return City|null
     */
    public static function findWithQuery($query)
    {
        $result = City::where('name', $query)->orWhere('index_name', $query)->first();

        // return result when found in database
        if ($result) return $result;

        // if not found, perform Google GeoCode search
        try {
            /** @var \Geocoder\Result\Geocoded $geocode */
            $geocode = \Geocoder::geocode('components=locality:' . urlencode($query));
        } catch (\Exception $e) {
            //echo $e->getMessage();
            return null;
        }

        // if no result from Google, return null
        if (!$geocode || empty($geocode->getCity())) return null;

        // else, create a new City, save to database, and return
        $city = City::loadFromGeoCoded($geocode);
        if ($city->save()) return $city;
        else return null;
    }

    /**
     * Create an instance of `City` from GeoCoded data
     *
     * @param $geocode \Geocoder\Result\Geocoded
     * @return City
     */
    public static function loadFromGeoCoded($geocode)
    {
        $city = new City();
        $city->name = $geocode->getCity();
        $city->index_name = mb_strtolower($city->name); // in case we have to deal with Unicode
        $city->longitude = $geocode->getLongitude();
        $city->latitude = $geocode->getLatitude();
        return $city;
    }

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
