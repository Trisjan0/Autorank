<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    /**
     * Display the system settings page.
     *
     * @return \Illuminate\View\View
     */
    public function showSystemSettings(): View
    {
        // Fetch the saved color or use a default for the color picker's initial value.
        $primaryColor = Setting::where('key', 'primary_color')->value('value') ?? '#262626';
        return view('system-settings', compact('primaryColor'));
    }

    /**
     * Handle the upload and update of the website logo.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $currentLogo = Setting::where('key', 'site_logo')->value('value');
        $path = $request->file('logo')->store('logos', 'public');

        Setting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => 'storage/' . $path]
        );

        if ($currentLogo && Storage::disk('public')->exists(str_replace('storage/', '', $currentLogo))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $currentLogo));
        }

        return response()->json([
            'message' => 'Logo updated successfully!',
            'path' => asset('storage/' . $path),
        ]);
    }

    /**
     * Update the website's primary color in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePrimaryColor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'primary_color' => 'sometimes|nullable|string|max:100',
        ]);

        Setting::updateOrCreate(
            ['key' => 'primary_color'],
            ['value' => $validated['primary_color']]
        );

        return response()->json(['success' => true, 'message' => 'Primary color updated successfully.']);
    }

    /**
     * Resets the website's primary color by setting it to null in the database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetThemeColor(): JsonResponse
    {
        Setting::updateOrCreate(
            ['key' => 'primary_color'],
            ['value' => null]
        );

        return response()->json(['success' => true, 'message' => 'Primary color has been reset.']);
    }

    /**
     * Generate a dynamic CSS file with the current theme settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateDynamicCss(): Response
    {
        // 1. Fetch the color value from the database.
        $colorFromDb = Setting::where('key', 'primary_color')->value('value');

        // 2. If the value is null or an empty string, use a hardcoded default color.
        //    Otherwise, use the color from the database.
        $color = !empty($colorFromDb) ? $colorFromDb : '#262626';

        // 3. Construct the CSS, ensuring there is always a valid color.
        $css = ":root { --primaryColor: {$color}; }";

        return new Response($css, 200, [
            'Content-Type' => 'text/css',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
