<?php

namespace Tests\Feature\Api;

use App\Models\Survey;
use App\Models\Device;
use App\Models\User;
use App\Models\UserRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class SurveyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_survey()
    {
        $user = User::factory()->create();
        $survey = Survey::factory()->create(['type' => 'range_1_5']);
        $device = Device::factory()->create();
        UserRating::create([
            'user_id' => $user->id,
            'survey_id' => $survey->id,
            'device_id' => $device->id,
            'result' => '4',
            'device' => 'TestDevice',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey');

        $response->assertOk()
            ->assertJsonStructure(['survey', 'result']);
    }

    public function test_authenticated_user_can_rate_survey()
    {
        $user = User::factory()->create();
        $survey = Survey::where('type', 'range_1_5')->first();
        $device = Device::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/survey/rate', [
            'survey_id' => $survey->id,
            'device_id' => $device->id,
            'result' => '5'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user_rating']);
    }

    public function test_authenticated_user_can_get_survey_report()
    {
        $user = User::factory()->create();
        $survey = Survey::factory()->create(['user_id' => $user->id]);
        UserRating::factory()->create(['survey_id' => $survey->id, 'result' => 4]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/survey/{$survey->id}/report");

        $response->assertOk()
            ->assertJsonStructure([
                'survey' => ['id', 'name'],
                'total_rating_count',
                'average_result',
                'device_ratings',
                'rating_distribution'
            ]);
    }

    public function test_authenticated_user_can_delete_survey()
    {
        $user = User::factory()->create();
        $survey = Survey::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/survey/{$survey->id}/delete");

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Survey deleted successfully.']);

        $this->assertDatabaseMissing('surveys', ['id' => $survey->id]);
    }

    public function test_authenticated_user_can_update_survey()
    {
        $user = User::factory()->create();
        $survey = Survey::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $updatedData = [
            'name' => 'Updated Survey',
            'question' => 'Updated Question'
        ];

        $response = $this->postJson("/api/survey/{$survey->id}/update", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('surveys', [
            'id' => $survey->id,
            'name' => 'Updated Survey',
            'question' => 'Updated Question'
        ]);
    }

    public function test_authenticated_user_can_list_all_surveys()
    {
        $user = User::factory()->create();
        Survey::factory()->count(3)->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/survey/list');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }
}
