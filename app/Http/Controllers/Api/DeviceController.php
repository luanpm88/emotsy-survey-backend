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
        $devices = $request->user()->devices;

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

        // Return the device details and the latest rating
        return response()->json($device);
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
        $device = new Device([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);
        $device->user_id = $request->user()->id;
        $device->save();

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

    public function destroy(Request $request, $device_id)
    {
        // Find the device by ID and ensure it belongs to the authenticated user
        $device = Device::where('user_id', $request->user()->id)->find($device_id);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found or you are not authorized to delete it.',
            ], 404);
        }

        // Delete the device
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device deleted successfully.',
        ], 200);
    }
}
