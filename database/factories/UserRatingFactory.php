<?php

namespace Database\Factories;

use App\Models\UserRating;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRatingFactory extends Factory
{
    protected $model = UserRating::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'survey_id' => \App\Models\Survey::factory(),
            'device_id' => \App\Models\Device::factory(),
            'result' => $this->faker->numberBetween(1, 5),
            'device' => $this->faker->userAgent,
        ];
    }
}
