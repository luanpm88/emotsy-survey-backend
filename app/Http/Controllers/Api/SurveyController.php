<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Device;
use App\Models\UserRating;

class SurveyController extends Controller
{
    public function list(Request $request)
    {
        // Fetch all surveys
        $surveys = $request->user()->surveys;

        $surveys = $surveys->map(function ($survey) use ($request) {
            // Get the latest rating for the survey by the authenticated user
            $latestRating = $request->user()->ratings()->where('survey_id', $survey->id)->latest()->first();

            // Return the survey details and the latest rating
            return [
                'survey' => $survey,
                'result' => $latestRating ? $latestRating->result : null,
            ];
        });

        // Return the survey details and the latest rating
        return response()->json($surveys);
    }

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
            'device_id' => 'required|exists:devices,id',
            'result' => 'required',
        ]);

        // Fetch the survey and the result from the request
        $survey = Survey::find($request->input('survey_id'));
        $device = Device::find($request->input('device_id'));
        $result = $request->input('result');

        // try {
            // Save the rating and return success response
            $userRating = UserRating::saveResult($request->user(), $survey, $device, $result, $request->header('User-Agent'));
        // } catch (\Exception $e) {
        //     // Return error response if saving the rating fails
        //     return response()->json(['error' => $e->getMessage()], 400);
        // }

        // Return success response with the saved rating
        return response()->json(['message' => 'Rating submitted successfully', 'user_rating' => $userRating], 201);
    }

    public function create(Request $request)
    {
        // Validate the incoming request
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'question' => 'required|string',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the survey
        $survey = new Survey([
            'name' => $request->input('name'),
            'question' => $request->input('question'),
            'type' => $request->input('type'),
        ]);
        $survey->user_id = $request->user()->id;
        $survey->save();

        return response()->json([
            'success' => true,
            'message' => 'Survey created successfully.',
            'data' => $survey
        ], 201);
    }

    public function update(Request $request, $survey_id)
    {
        // Validate the incoming request
        $validator = \Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'question' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the survey by ID
        $survey = Survey::find($survey_id);

        if (!$survey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found.'
            ], 404);
        }

        // Update the survey with validated data
        $survey->update($request->only(['name', 'question', 'type']));

        return response()->json([
            'success' => true,
            'message' => 'Survey updated successfully.',
            'data' => $survey
        ], 200);
    }

    public function destroy(Request $request, $survey_id)
    {
        // Find the survey by ID and ensure it belongs to the authenticated user
        $survey = Survey::where('user_id', $request->user()->id)->find($survey_id);

        if (!$survey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found or you are not authorized to delete it.',
            ], 404);
        }

        // Delete the survey
        $survey->delete();

        return response()->json([
            'success' => true,
            'message' => 'Survey deleted successfully.',
        ], 200);
    }

    public function copy(Request $request, $survey_id)
    {
        // Find the survey by ID and ensure it belongs to the authenticated user
        $originalSurvey = Survey::where('user_id', $request->user()->id)->find($survey_id);

        if (!$originalSurvey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found or you are not authorized to clone it.',
            ], 404);
        }

        // Clone the survey
        $clonedSurvey = $originalSurvey->replicate();
        if ($request->name) {
            $clonedSurvey->name = $request->name;
        } else {
            $clonedSurvey->name .= ' (Copy)'; // Optionally modify a field, like appending "Copy" to the title
        }
            
        $clonedSurvey->save();

        return response()->json([
            'success' => true,
            'message' => 'Survey cloned successfully.',
            'data' => $clonedSurvey
        ], 201);
    }

    public function report($id)
    {
        // Fetch the survey
        $survey = Survey::findOrFail($id);

        // Get all ratings for the survey
        $ratings = UserRating::where('survey_id', $id)->get();

        // Total rating count
        $totalRatingCount = $ratings->count();

        // Average result
        $averageResult = $totalRatingCount > 0 ? $ratings->avg('result') : 0;

        // Device-wise ratings summary
        $deviceRatings = $ratings->groupBy('device_id')->map(function ($deviceRatings) {
            return [
                'total_rate' => $deviceRatings->count(),
                'average' => $deviceRatings->avg('result'),
            ];
        })->toArray();

        // Distribution of ratings (1-5)
        $ratingDistribution = array_fill(1, 5, 0);
        foreach ($ratings as $rating) {
            $ratingDistribution[$rating->result]++;
        }

        // Response
        return response()->json([
            'survey' => [
                'id' => $survey->id,
                'name' => $survey->name,
            ],
            'total_rating_count' => $totalRatingCount,
            'average_result' => $averageResult,
            'device_ratings' => $deviceRatings,
            'rating_distribution' => $ratingDistribution,
        ]);
    }
}
