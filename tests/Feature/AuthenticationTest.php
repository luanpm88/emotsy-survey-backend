<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Survey;
use App\Models\Device;
use App\Models\UserRating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ],
                'access_token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);
    }

    public function test_user_cannot_register_with_invalid_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_name' => 'test_device'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user',
                'access_token',
                'token_type'
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
            'device_name' => 'test_device'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_get_their_info()
    {
        $user = User::factory()->create();
        
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJson($user->toArray());
    }

    public function test_unauthenticated_user_cannot_get_user_info()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_token()
    {
        $user = User::factory()->create();
        
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/user/create-token', [
            'token_name' => 'test_token'
        ]);

        var_dump($response->json());

        $response->assertOk()
            ->assertJsonStructure(['access_token']);
    }

    public function test_unauthenticated_user_cannot_create_token()
    {
        $response = $this->postJson('/api/user/create-token', [
            'token_name' => 'test_token'
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_survey()
    {
        $user = User::factory()->create();
        $survey = Survey::where('type', 'range_1_5')->first();
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
        $survey = Survey::create([
            'user_id' => $user->id,
            'name' => 'Test Survey',
            'question' => 'Test Question',
            'type' => 'range_1_5'
        ]);
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
}