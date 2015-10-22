<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Tweet
 *
 * @property string $id
 * @property string $content
 * @property string $username
 * @property string $user_display_name
 * @property string $user_avatar
 * @property string $latitude
 * @property string $longitude
 * @property \Carbon\Carbon $fetched_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $city_id
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereUserDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereUserAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereLongitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereFetchedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereCityId($value)
 */
class Tweet extends Model
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'tweet';

    /**
     * List of properties will be transformed to `Carbon` object
     * @var array
     */
    protected $dates = ['fetched_at'];

    /**
     * Fetch tweets by a `City` instance
     *
     * @param \App\City $city The city to fetch tweets
     * @param string $radius Radius of fetching, unit can be `km` or `mi`
     * @param int $limit Limit number of records to return
     * @return Tweet[]
     */
    public static function fetchByCity($city, $radius = '50km', $limit = 20)
    {
        if (!isset($city->fetched_at) || $city->fetched_at->addHour(1)->lt(Carbon::now())) {
            // purge old tweets created in the last 1 hour
            Tweet::whereCityId($city->id)->delete();

            // build up query for geo search
            $geoCodePattern = '%s,%s,%s';
            $geoCode = sprintf($geoCodePattern, $city->latitude, $city->longitude, $radius);

            // if tweets from this city are fetched more than 1 hour ago
            // then fetch again from twitter API
            $twitterQuery = [
                'q' => $city->name,
                'geocode' => $geoCode,
                'count' => $limit
            ];

            $rawTweets = \Twitter::getSearch($twitterQuery);

            // filter out tweets that don't have geo data
            $rawTweets = array_filter($rawTweets->statuses, function($t) {
                return isset($t->coordinates) && !empty($t->coordinates->coordinates);
            });

            $tweets = [];
            foreach ($rawTweets as $rawTweet) {
                $tweet = Tweet::loadFromTwitterApi($rawTweet, $city);
                if ($tweet->save()) $tweets[] = $tweet;
            }
            return $tweets;
        } else {
            // if tweets from this city are fetched from API less than 1 hour ago
            // then load from database
            return Tweet::whereCityId($city->id)->all();
        }
    }

    /**
     * Create an instance of `Tweet` from data returned from Twitter API
     *
     * @param $data \stdClass
     * @param $city \App\City
     * @return Tweet
     */
    public static function loadFromTwitterApi($data, $city)
    {
        $tweet = new Tweet();
        $tweet->content = $data->text;
        $tweet->username = $data->user->name;
        $tweet->user_display_name = $data->user->screen_name;
        $tweet->user_avatar = str_replace('_normal.', '_bigger.', $data->user->profile_image_url);
        $tweet->latitude = (string)$data->coordinates->coordinates[0];
        $tweet->longitude = (string)$data->coordinates->coordinates[1];
        $tweet->city_id = $city->id;
        $tweet->fetched_at = Carbon::now();
        return $tweet;
    }
}
