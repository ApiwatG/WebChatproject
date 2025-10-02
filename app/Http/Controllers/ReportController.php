<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'offender_id' => 'required|exists:users,id',
        'message' => 'required|string|max:1000',
    ]);

    Report::create([
        'reporter_id' => auth()->id(),
        'offender_id' => $request->offender_id,
        'message' => $request->message,
    ]);

    return response()->json(['status' => 'Report saved successfully']);
}

public function index()
{
    $reports = Report::with('user')->latest()->get();
    return view('reports.index', compact('reports'));
}


}
