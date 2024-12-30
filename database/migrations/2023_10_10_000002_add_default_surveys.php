<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class AddDefaultSurveys extends Migration
{
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => 'SurveySeeder',
        ]);
    }

    public function down()
    {
        // Optionally, you can add code here to reverse the seeder if needed.
    }
}
