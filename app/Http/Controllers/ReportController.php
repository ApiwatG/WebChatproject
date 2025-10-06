<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(Request $request, $offenderId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'reported_message' => 'nullable|string|max:1000',
        ]);

        Report::create([
            'reporter_id' => auth()->id(),
            'offender_id' => $offenderId,
            'message' => $request->message,
            'Report_message' => $request->reported_message ?? 'No specific message reported',
        ]);

        return back()->with('success', 'Report submitted successfully.');
    }

    public function index()
    {
        $reports = Report::with(['reporter', 'offender'])->latest()->get();
        return view('reports.index', compact('reports'));
    }
}