<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Log;

class AHPService
{
    // DBM-CHED (2022) KRA Category Point Caps
    protected const KRA_CAPS = [
        'kra1' => [
            'total' => 100,
            'sub_caps' => [
                'teaching_effectiveness' => 60,
                'instructional_materials' => 30,
                'mentorship_services' => 10,
            ]
        ],
        'kra2' => [
            'total' => 100,
            'sub_caps' => [
                'research_outputs' => 100,
                'inventions' => 100,
                'creative_works' => 100,
            ]
        ],
        'kra3' => [
            'total' => 100,
            'sub_caps' => [
                'service_to_institution' => 50,
                'service_to_community' => 30,
                'extension_involvement' => 20,
            ]
        ],
        'kra4' => [
            'total' => 100,
            'sub_caps' => [
                'professional_organizations' => 20,
                'continuing_development' => 60,
                'awards_and_recognitions' => 20,
            ]
        ],
    ];

    /**
     * Calculate the final general score for a given application.
     *
     * @param Application $application
     * @return float
     */
    public function calculateGeneralScore(Application $application): float
    {
        // Step 1: Get the raw KRA scores from the application model.
        // These scores are assumed to be pre-summed from the evaluation process.
        $rawScores = [
            'kra1' => $application->kra1_score ?? 0,
            'kra2' => $application->kra2_score ?? 0,
            'kra3' => $application->kra3_score ?? 0,
            'kra4' => $application->kra4_score ?? 0,
        ];

        // Step 2: Apply the DBM-CHED caps to each KRA total score.
        $cappedScores = [
            'kra1' => min($rawScores['kra1'], self::KRA_CAPS['kra1']['total']),
            'kra2' => min($rawScores['kra2'], self::KRA_CAPS['kra2']['total']),
            'kra3' => min($rawScores['kra3'], self::KRA_CAPS['kra3']['total']),
            'kra4' => min($rawScores['kra4'], self::KRA_CAPS['kra4']['total']),
        ];

        // Step 3: Fetch the AHP weights for the rank being applied for.
        // The position relationship on the Application model must be loaded.
        $position = $application->position;

        if (!$position) {
            Log::error("AHP Calculation Failed: Position not found for Application ID {$application->id}");
            return 0.0;
        }

        $weights = [
            'kra1' => $position->kra1_weight / 100, // Assuming weights are stored as percentages (e.g., 60 for 60%)
            'kra2' => $position->kra2_weight / 100,
            'kra3' => $position->kra3_weight / 100,
            'kra4' => $position->kra4_weight / 100,
        ];

        // Step 4: Calculate the final weighted score.
        $finalScore = 0;
        $finalScore += $cappedScores['kra1'] * $weights['kra1'];
        $finalScore += $cappedScores['kra2'] * $weights['kra2'];
        $finalScore += $cappedScores['kra3'] * $weights['kra3'];
        $finalScore += $cappedScores['kra4'] * $weights['kra4'];

        return (float) $finalScore;
    }
}
