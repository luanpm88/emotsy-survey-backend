<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    public $default = 'range_1_5';

    public $types = [
        [
            'name' => 'range_1_5',
            'possible_values' => ['1', '2', '3', '4', '5'],
        ], [
            'name' => 'happy_unhappy',
            'possible_values' => ['happy', 'unhappy'],
        ],
    ];

    public function ratings()
    {
        return $this->hasMany(UserRating::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_ratings');
    }
}
