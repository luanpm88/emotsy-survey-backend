<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    public $default = 'range_1_5';

    protected $fillable = [
        'name',
        'question',
        'type',
    ];

    public $types = [
        [
            'name' => 'range_1_5',
            'possible_values' => ['1', '2', '3', '4', '5'],
        ], [
            'name' => 'happy_unhappy',
            'possible_values' => ['1', '2', '3', '4', '5'], // Từ buồn:1 đến vui:3 ... rất vui:5
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

    public function getPossibleValues()
    {
        $type = collect($this->types)->firstWhere('name', $this->type);

        return $type['possible_values'];
    }
}
