<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * Update the authenticated user's theme preference (light/dark mode).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTheme(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        $user = $request->user();
        $user->theme = $validated['theme'];
        $user->save();

        return response()->json(['success' => true, 'message' => 'Theme preference updated.']);
    }
}
