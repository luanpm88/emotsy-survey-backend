@extends('layouts.main', [
    'menu' => 'survey',
])

@section('title', 'Create Survey')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Create Survey</h1>
        <form action="{{ route('surveys.store') }}" method="POST">
            @csrf
            @include('surveys._form')
            
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
