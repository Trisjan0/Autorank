<?php

namespace App\Http\Controllers\Admin;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public function index()
    {
        // Fetch all applications from the database
        $applications = Application::all();

        return view('admin.application-page', compact('applications'));
    }
}
