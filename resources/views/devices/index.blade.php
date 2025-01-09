@extends('layouts.main', [
    'menu' => 'device',
])

@section('title', 'Devices')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Devices</h1>
        <a href="{{ route('devices.create') }}" class="btn btn-primary mb-3">Create New Device</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($devices as $device)
                    <tr>
                        <td>
                            <a href="{{ route('devices.show', $device->id) }}">{{ $device->name }}</a>
                        </td>
                        <td>
                            {{ $device->description }}
                        </td>
                        <td>
                            <a href="{{ route('devices.edit', $device->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('devices.destroy', $device->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
