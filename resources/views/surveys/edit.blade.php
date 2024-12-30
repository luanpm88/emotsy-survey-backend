@extends('layouts.main')

@section('title', 'Edit Survey')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Edit Survey</h1>
        <form action="{{ route('surveys.update', $survey->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ $survey->name }}" required>
            </div>
            <div class="form-group">
                <label for="question">Question:</label>
                <input type="text" id="question" name="question" class="form-control" value="{{ $survey->question }}" required>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" class="form-control" value="{{ $survey->type }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
