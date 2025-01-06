<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::all();
        return view('surveys.index', compact('surveys'));
    }

    public function create()
    {
        $survey = new Survey();

        return view('surveys.create', compact('survey'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'question' => 'required|string',
            'type' => 'required|string',
        ]);

        Survey::create($validatedData);

        return redirect()->route('surveys.index')->with('success', 'Survey created successfully.');
    }

    public function show($id)
    {
        $survey = Survey::findOrFail($id);

        // stats
        $stats = collect([]);

        // 
        foreach ($survey->getPossibleValues() as $value) {
            $stats->push([
                'name' => $value,
                'value' => $survey->ratings()->where('result', $value)->count(),
            ]);
        }

        return view('surveys.show', compact('survey', 'stats'));
    }

    public function edit($id)
    {
        $survey = Survey::findOrFail($id);
        return view('surveys.edit', compact('survey'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'question' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
        ]);

        $survey = Survey::findOrFail($id);
        $survey->update($validatedData);

        return redirect()->route('surveys.index')->with('success', 'Survey updated successfully.');
    }

    public function destroy($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();

        return redirect()->route('surveys.index')->with('success', 'Survey deleted successfully.');
    }
}
