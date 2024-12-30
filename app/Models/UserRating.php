<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'survey_id', 'result', 'device'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
