<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;

class SurveySeeder extends Seeder
{
    public function run()
    {
        Survey::truncate();

        Survey::create([
            'name' => 'Customer Satisfaction Survey',
            'question' => 'How satisfied are you with our service?',
            'type' => 'range_1_5',
        ]);

        Survey::create([
            'name' => 'Employee Happiness Survey',
            'question' => 'Are you happy with your job?',
            'type' => 'happy_unhappy',
        ]);
    }
}
