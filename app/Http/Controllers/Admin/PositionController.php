<?php

namespace App\Http\Controllers\Admin;

use App\Models\Position;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    /**
     * Update the availability status of a position.
     */
    public function toggle(Position $position): JsonResponse
    {
        // Update the position's availability
        $position->update(['is_available' => !$position->is_available]);

        // Return a JSON response with the new status
        return response()->json([
            'success' => true,
            'is_available' => $position->is_available,
            'message' => 'Position status updated successfully.'
        ]);
    }
}
