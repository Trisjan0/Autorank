<?php

namespace App\Http\Controllers\Admin;

use App\Models\Position;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Update the availability status of a position.
     */
    public function toggle(Request $request, Position $position): JsonResponse
    {
        $isAvailable = !$position->is_available;
        $availableSlots = $isAvailable ? $request->input('available_slots', 0) : 0;

        // Update the position's availability and slots
        $position->update([
            'is_available' => $isAvailable,
            'available_slots' => $availableSlots,
        ]);

        // Return a JSON response with the full position object
        return response()->json([
            'success' => true,
            'position' => $position->fresh(), // Send back the updated model
            'message' => 'Position status updated successfully.'
        ]);
    }
}
