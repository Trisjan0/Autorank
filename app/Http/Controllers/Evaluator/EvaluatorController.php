<?php

namespace App\Http\Controllers\Evaluator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EvaluatorController extends Controller
{
    public function showEvaluateApplications()
    {
        return view('evaluator.evaluate-applications-page');
    }
}
