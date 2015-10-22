<?php

namespace App;

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
 * @property string $fetched_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
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
 */
class Tweet extends Model
{
    /** @var string Table name */
    protected $table = 'tweet';
}
