@extends('layouts.main', [
    'menu' => 'survey',
])

@section('title', 'Edit Survey')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Survey</h1>
        <form action="{{ route('surveys.update', $survey->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('surveys._form')
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
