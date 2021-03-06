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
 * @property string $city_id
 * @property string $tweet_id
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
 * @method static \Illuminate\Database\Query\Builder|\App\Tweet whereTweetId($value)
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
        // save history
        $userId = \Cookie::get('id');
        if ($userId) {
            $history = new History();
            $history->city_name = $city->name;
            $history->user_id = $userId;
            $history->place_id = $city->place_id;
            $history->save();
        }

        if (!isset($city->fetched_at) || $city->fetched_at->addHour(1)->lt(Carbon::now())) {
            // purge old tweets created in the last 1 hour
            Tweet::whereCityId($city->id)->delete();
            $city->fetched_at = Carbon::now();
            $city->save();

            // build up query for geo search
            $geoCodePattern = '%s,%s,%s';
            $geoCode = sprintf($geoCodePattern, $city->latitude, $city->longitude, $radius);

            $query = sprintf('%s OR #%s', $city->name, $city->name);

            // if tweets from this city are fetched more than 1 hour ago
            // then fetch again from twitter API
            $twitterQuery = [
                'q' => $query,
                'geocode' => $geoCode,
                'count' => $limit
            ];

            $rawTweets = \Twitter::getSearch($twitterQuery);

            // filter out tweets that don't have geo data and empty content
            $rawTweets = array_filter($rawTweets->statuses, function($t) {
                return isset($t->coordinates) && !empty($t->coordinates->coordinates) && !empty($t->text);
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
            return Tweet::whereCityId($city->id)->get();
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
        $tweet->tweet_id = (string)$data->id;
        $tweet->content = $data->text;
        $tweet->username = $data->user->name;
        $tweet->user_display_name = $data->user->screen_name;
        $tweet->user_avatar = $data->user->profile_image_url;
        $tweet->latitude = (string)$data->coordinates->coordinates[1];
        $tweet->longitude = (string)$data->coordinates->coordinates[0];
        $tweet->city_id = $city->id;
        $tweet->fetched_at = Carbon::parse($data->created_at);
        return $tweet;
    }
}
