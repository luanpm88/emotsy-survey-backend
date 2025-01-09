@extends('layouts.main', [
    'menu' => 'device',
])

@section('title', 'Edit Device')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Device</h1>
        <form action="{{ route('devices.update', $device->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('devices._form')
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
