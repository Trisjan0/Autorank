<?php

namespace App\Http\Controllers;

use App\Models\ApplicationModel;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        // Fetch all applications from the database
        $applications = ApplicationModel::all();

        return view('admin.application-page', compact('applications'));
    }
}
