<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApplicationModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public function index()
    {
        // Fetch all applications from the database
        $applications = ApplicationModel::all();

        return view('admin.application-page', compact('applications'));
    }
}
