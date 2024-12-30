@extends('layouts.main')

@section('title', 'Surveys')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Surveys</h1>
        <a href="{{ route('surveys.create') }}" class="btn btn-primary mb-3">Create New Survey</a>
        <ul class="list-group">
            @foreach ($surveys as $survey)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('surveys.show', $survey->id) }}">{{ $survey->name }}</a>
                    <div>
                        <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('surveys.destroy', $survey->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
