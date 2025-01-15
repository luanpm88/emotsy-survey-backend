<?php

namespace Tests\Feature\Api;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class DeviceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_devices()
    {
        $user = User::factory()->create();
        Device::factory()->count(3)->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/device/list');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_authenticated_user_can_view_device()
    {
        $user = User::factory()->create();
        $device = Device::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/device/{$device->id}");

        $response->assertStatus(200)
            ->assertJson($device->toArray());
    }

    public function test_authenticated_user_can_create_device()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $deviceData = [
            'name' => 'Test Device',
            'description' => 'Test Description'
        ];

        $response = $this->postJson('/api/device/create', $deviceData);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('devices', [
            'name' => 'Test Device',
            'description' => 'Test Description',
            'user_id' => $user->id
        ]);
    }

    public function test_authenticated_user_can_update_device()
    {
        $user = User::factory()->create();
        $device = Device::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $updatedData = [
            'name' => 'Updated Device',
            'description' => 'Updated Description'
        ];

        $response = $this->postJson("/api/device/{$device->id}/update", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('devices', [
            'id' => $device->id,
            'name' => 'Updated Device',
            'description' => 'Updated Description'
        ]);
    }

    public function test_authenticated_user_can_delete_device()
    {
        $user = User::factory()->create();
        $device = Device::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/device/{$device->id}/delete");

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Device deleted successfully.']);

        $this->assertDatabaseMissing('devices', ['id' => $device->id]);
    }
}
