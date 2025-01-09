<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        $device = new Device();

        return view('devices.create', compact('device'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'sometimes|string',
        ]);

        // create
        $device = new Device($validatedData);
        $device->user_id  = $request->user()->id;
        $device->save();

        return redirect()->route('devices.index')->with('success', 'Device created successfully.');
    }

    public function show($id)
    {
        $device = Device::findOrFail($id);

        return view('devices.show', compact('device'));
    }

    public function edit($id)
    {
        $device = Device::findOrFail($id);
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $device = Device::findOrFail($id);
        $device->update($validatedData);

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}
