@extends('layouts.main', [
    'menu' => 'survey',
])

@section('title', 'Create Survey')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Create Survey</h1>
        <form action="{{ route('surveys.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="question">Question:</label>
                <input type="text" id="question" name="question" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
