<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\History
 *
 * @property integer $id
 * @property string $user_id
 * @property string $city_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\History whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\History whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\History whereCityName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\History whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\History whereUpdatedAt($value)
 * @property string $place_id
 * @method static \Illuminate\Database\Query\Builder|\App\History wherePlaceId($value)
 */
class History extends Model
{
    protected $table = 'history';
}
