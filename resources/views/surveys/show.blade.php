@extends('layouts.main')

@section('title', $survey->name)

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">{{ $survey->name }}</h1>
        <p>{{ $survey->question }}</p>
        <p>Type: {{ $survey->type }}</p>
        
        <hr>
        <h5>Results</h5>
        @if ($survey->users()->count())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($survey->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->ratings()
                                ->bySurvey($survey)
                                ->latest()
                                ->first()->result }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">There are no results yet!</div>
        @endif

        <a href="{{ route('surveys.index') }}" class="btn btn-secondary">Back to Surveys</a>
    </div>
@endsection
