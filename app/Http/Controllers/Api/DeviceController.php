<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\UserRating;

class DeviceController extends Controller
{
    public function list(Request $request)
    {
        // Fetch all devices
        $devices = Device::all();

        // Return the device details and the latest rating
        return response()->json($devices);
    }

    /**
     * Get the device details and the latest rating for the authenticated user.
     */
    public function show(Request $request, $id)
    {
        // Fetch the device with the specified type
        $device = Device::find($id);

        // Return error if no device is found
        if (!$device) {
            return response()->json(['error' => 'No device found with id: ' . $id], 404);
        }

        // Get the latest rating for the device by the authenticated user
        $latestRating = $request->user()->ratings()->where('device_id', $device->id)->latest()->first();

        // Return the device details and the latest rating
        return response()->json([
            'device' => $device,
            'result' => $latestRating ? $latestRating->result : null,
        ]);
    }

    public function create(Request $request)
    {
        // Validate the incoming request
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the device
        $device = Device::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device created successfully.',
            'data' => $device
        ], 201);
    }

    public function update(Request $request, $device_id)
    {
        // Validate the incoming request
        $validator = \Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the device by ID
        $device = Device::find($device_id);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found.'
            ], 404);
        }

        // Update the device with validated data
        $device->update($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Device updated successfully.',
            'data' => $device
        ], 200);
    }
}