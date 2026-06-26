<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItWorkHubController extends Controller
{
    public function dashboard()
    {
        return view('it-work-hub.dashboard');
    }

    public function longlist()
    {
        return view('it-work-hub.longlist');
    }

    public function create()
    {
        return view('it-work-hub.create');
    }

    public function show($id)
    {
        return view('it-work-hub.show', compact('id'));
    }

    public function activities($id)
    {
        return view('it-work-hub.activities', compact('id'));
    }

    public function repository()
    {
        return view('it-work-hub.repository');
    }
}
