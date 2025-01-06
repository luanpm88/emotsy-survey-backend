<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\UserRating;

class SurveyController extends Controller
{
    /**
     * Get the survey details and the latest rating for the authenticated user.
     */
    public function survey(Request $request)
    {
        // Initialize survey and get the default type
        $survey = new Survey();
        $type = collect($survey->types)->firstWhere('name', $survey->default);

        // Check if a specific type is requested
        if ($request->has('type')) {
            $requestedType = $request->input('type');
            $type = collect($survey->types)->firstWhere('name', $requestedType);

            // Return error if the requested type is invalid
            if (!$type) {
                return response()->json(['error' => 'Invalid type: ' . $requestedType], 400);
            }
        }

        // Fetch the survey with the specified type
        $survey = Survey::where('type', $type)->first();

        // Return error if no survey is found
        if (!$survey) {
            return response()->json(['error' => 'No survey found with type: ' . $type['name']], 404);
        }

        // Get the latest rating for the survey by the authenticated user
        $latestRating = $request->user()->ratings()->where('survey_id', $survey->id)->latest()->first();

        // Return the survey details and the latest rating
        return response()->json([
            'survey' => $survey,
            'result' => $latestRating ? $latestRating->result : null,
        ]);
    }

    /**
     * Get the survey details and the latest rating for the authenticated user.
     */
    public function show(Request $request, $id)
    {
        // Fetch the survey with the specified type
        $survey = Survey::find($id);

        // Return error if no survey is found
        if (!$survey) {
            return response()->json(['error' => 'No survey found with id: ' . $id], 404);
        }

        // Get the latest rating for the survey by the authenticated user
        $latestRating = $request->user()->ratings()->where('survey_id', $survey->id)->latest()->first();

        // Return the survey details and the latest rating
        return response()->json([
            'survey' => $survey,
            'result' => $latestRating ? $latestRating->result : null,
        ]);
    }

    /**
     * Submit a rating for a survey by the authenticated user.
     */
    public function rate(Request $request)
    {
        // Validate the request data
        $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'result' => 'required',
        ]);

        // Fetch the survey and the result from the request
        $survey = Survey::find($request->input('survey_id'));
        $result = $request->input('result');

        try {
            // Save the rating and return success response
            $userRating = UserRating::saveResult($request->user(), $survey, $result, $request->header('User-Agent'));
        } catch (\Exception $e) {
            // Return error response if saving the rating fails
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Return success response with the saved rating
        return response()->json(['message' => 'Rating submitted successfully', 'user_rating' => $userRating], 201);
    }
}
