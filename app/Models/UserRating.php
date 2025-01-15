<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'survey_id', 'device_id', 'result', 'device'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public static function saveResult($user, $survey, $device, $result, $fromDevice)
    {
        $validType = collect($survey->types)->firstWhere('name', $survey->type);

        if (!$validType) {
            throw new \Exception('Invalid type: ' . $survey->type);
        }

        if (!in_array($result, $validType['possible_values'])) {
            throw new \Exception('Invalid result value. Value: ' . $result . '. Except values: ' . json_encode($validType['possible_values']));
        }

        static::where('user_id', $user->id)
            ->where('survey_id', $survey->id)->delete();

        return static::create([
            'user_id' => $user->id,
            'survey_id' => $survey->id,
            'device_id' => $device->id,
            'result' => $result,
            'device' => $fromDevice,
        ]);
    }

    public static function scopeBySurvey($query, $survey)
    {
        return $query->where('survey_id', $survey->id);
    }
}
