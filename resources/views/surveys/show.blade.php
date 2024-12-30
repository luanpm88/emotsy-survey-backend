@extends('layouts.main')

@section('title', $survey->name)

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">{{ $survey->name }}</h1>
        <p>{{ $survey->question }}</p>
        <p>Type: {{ $survey->type }}</p>
        <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Back to Surveys</a>
    </div>
@endsection
