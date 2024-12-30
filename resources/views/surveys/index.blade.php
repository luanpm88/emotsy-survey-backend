@extends('layouts.main', [
    'menu' => 'survey',
])

@section('title', 'Surveys')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Surveys</h1>
        <a href="{{ route('surveys.create') }}" class="btn btn-primary mb-3">Create New Survey</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name/Question</th>
                    <th>Type</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($surveys as $survey)
                    <tr>
                        <td>
                            <a href="{{ route('surveys.show', $survey->id) }}">{{ $survey->name }}</a>
                            <br>
                            {{ $survey->question }}
                        </td>
                        <td>{{ $survey->type }}</td>
                        <td>{{ $survey->ratings()->count() }} result(s)</td>
                        <td>
                            <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('surveys.destroy', $survey->id) }}" method="POST" style="display:inline;">
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
