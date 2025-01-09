@extends('layouts.main', [
    'menu' => 'device',
])

@section('title', $device->name)

@section('content')
    <script src="
    https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js
    "></script>

    <div class="container mt-5">
        <h1 class="mb-4">{{ $device->name }}</h1>
        <h4>{{ $device->description }}</h4>
            

        <a href="{{ route('devices.index') }}" class="btn btn-secondary">Back to Devices</a>
    </div>
@endsection
