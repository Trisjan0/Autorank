<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\PhoneVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | !! THIS IS FOR DEMO / SIMULATION ONLY !!
    |--------------------------------------------------------------------------
    */
    public function sendOtpForPhoneNumber(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'phone_number' => [
                'required',
                'string',
                'digits:11',
                'starts_with:09',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'phone_number.unique' => 'This phone number is already registered.',
        ]);

        $phoneNumber = $request->phone_number;

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database
        PhoneVerification::where('user_id', $user->id)
            ->where('phone_number', $phoneNumber)
            ->where('expires_at', '>', Carbon::now())
            ->delete();

        PhoneVerification::create([
            'user_id' => $user->id,
            'phone_number' => $phoneNumber,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // --- SIMULATED SMS SENDING PART ---
        Log::info("SIMULATED OTP for {$phoneNumber}: {$otp}");
        if (app()->environment('local', 'staging')) { // Only show in non-production environments
            return response()->json([
                'message' => 'OTP generated successfully! Check logs for OTP (Dev Mode).',
                'success' => true,
                'otp' => $otp // <--- ONLY FOR DEVELOPMENT/TESTING! REMOVE IN PRODUCTION!
            ]);
        }
        // --- END SIMULATED SMS SENDING PART ---

        return response()->json([
            'message' => 'OTP sent successfully!',
            'success' => true
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | !! THIS IS FOR DEMO / SIMULATION ONLY !!
    |--------------------------------------------------------------------------
    */
    public function verifyOtpAndSavePhoneNumber(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        /** @var \App\Models\User $user */

        // 1. Validate the incoming data
        $request->validate([
            'phone_number' => ['required', 'string', 'digits:11', 'starts_with:09'],
            'otp_code' => ['required', 'string', 'digits:6'],
        ]);

        $phoneNumber = $request->phone_number;
        $otpCode = $request->otp_code;

        // 2. Find the latest valid OTP for this user and phone number
        $verification = PhoneVerification::where('user_id', $user->id)
            ->where('phone_number', $phoneNumber)
            ->where('expires_at', '>', Carbon::now())
            ->latest() // Get the latest one if multiple exist
            ->first();

        // 3. Verify OTP
        if (!$verification || $verification->otp !== $otpCode) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        // 4. OTP is valid, save the phone number to the user
        $user->phone_number = $phoneNumber;
        $user->save();

        // 5. Delete the used OTP record
        $verification->delete();

        return response()->json([
            'message' => 'Phone number updated successfully!',
            'phone_number' => $user->phone_number
        ]);
    }
}
