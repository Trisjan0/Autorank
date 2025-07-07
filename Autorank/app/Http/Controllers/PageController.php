<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function showApplicationsPage()
    {
        return view('application-page');
    }

    public function showProfilePage()
    {
        return view('profile-page');
    }

    public function showResearchDocumentsPage()
    {
        return view('research-documents-page');
    }

    public function showEvaluationsPage()
    {
        return view('evaluations-page');
    }

    public function showEventParticipationsPage()
    {
        return view('event-participations-page');
    }
}
