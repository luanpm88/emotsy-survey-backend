@extends('layouts.main', [
    'menu' => 'device',
])

@section('title', 'Create Device')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Create Device</h1>
        <form action="{{ route('devices.store') }}" method="POST">
            @csrf
            @include('devices._form')
            
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
