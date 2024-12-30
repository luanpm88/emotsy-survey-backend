<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Survey;
use App\Models\UserRating;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
 
    return ['access_token' => $token->plainTextToken];
})->middleware('auth:sanctum');

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);
 
    $user = User::where('email', $request->email)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
 
    return response()->json([
        'user' => $user,
        'access_token' => $user->createToken($request->device_name)->plainTextToken,
        'token_type' => 'Bearer',
    ], 201);
});

Route::post('/register', function (Request $request) {
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed', // password_confirmation required
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'access_token' => $token,
        'token_type' => 'Bearer',
    ], 201);
});

Route::get('/survey', function (Request $request) {
    $survey = new Survey();
    $type = collect($survey->types)->firstWhere('name', $survey->default);

    if ($request->has('type')) {
        $requestedType = $request->input('type');
        $type = collect($survey->types)->firstWhere('name', $requestedType);

        if (!$type) {
            return response()->json(['error' => 'Invalid type: ' . $request->has('type')], 400);
        }
    }

    $survey = Survey::where('type', $type)->first();

    if (!$survey) {
        return response()->json(['error' => 'No survey found with type: ' . $type['name']], 404);
    }

    $latestRating = $request->user()->ratings()->where('survey_id', $survey->id)->latest()->first();

    return response()->json([
        'survey' => $survey,
        'result' => $latestRating ? $latestRating->result : null,
    ]);
})->middleware('auth:sanctum');

Route::post('/survey/rate', function (Request $request) {
    $request->validate([
        'survey_id' => 'required|exists:surveys,id',
        'result' => 'required',
    ]);

    $survey = Survey::find($request->input('survey_id'));
    $result = $request->input('result');

    $validType = collect($survey->types)->firstWhere('name', $survey->type);

    if (!$validType) {
        return response()->json(['error' => 'Invalid type: ' . $survey->type], 400);
    }

    if (!in_array($result, $validType['possible_values'])) {
        return response()->json(['error' => 'Invalid result value'], 400);
    }

    $userRating = UserRating::create([
        'user_id' => $request->user()->id,
        'survey_id' => $survey->id,
        'result' => $result,
        'device' => $request->header('User-Agent'),
    ]);

    return response()->json(['message' => 'Rating submitted successfully', 'user_rating' => $userRating], 201);
})->middleware('auth:sanctum');