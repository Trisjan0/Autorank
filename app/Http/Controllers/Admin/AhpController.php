<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AhpCriterion;
use App\Models\AhpWeight;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AhpController extends Controller
{
    /**
     * Show the form for setting AHP weights.
     */
    public function showWeightsForm()
    {
        // Fetch all top-level criteria (those with no parent)
        $criteria = AhpCriterion::whereNull('parent_id')->get();

        // Fetch existing weights and map them to criteria IDs for the form
        $weights = AhpWeight::pluck('weight', 'criterion_id')->toArray();

        // View path
        return view('admin.manage-criterion-weights', compact('criteria', 'weights'));
    }

    /**
     * Update the AHP weights.
     */
    public function updateWeights(Request $request)
    {
        // 1. Validate the incoming request data
        $validatedData = $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|between:0,1',
        ]);

        // 2. Calculate the sum of weights
        $totalWeight = array_sum($validatedData['weights']);

        // 3. Compare the sum with 1.0 using a tolerance
        $epsilon = 0.0001; // A small tolerance for floating-point comparison

        if (abs($totalWeight - 1.0) > $epsilon) {
            return redirect()->back()->withErrors(['weights' => 'The sum of all weights must equal 1.0. Your total was ' . number_format($totalWeight, 4) . '.']);
        }

        // 4. Begin a database transaction for data integrity
        DB::transaction(function () use ($validatedData) {
            // 5. Update or create the weights in the database
            foreach ($validatedData['weights'] as $criterionId => $weight) {
                AhpWeight::updateOrCreate(
                    ['criterion_id' => $criterionId],
                    ['weight' => $weight]
                );
            }
        });

        // 6. Redirect back with a success message
        return redirect()->back()->with('success', 'AHP weights have been updated successfully!');
    }
}
