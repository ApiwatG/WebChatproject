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
            'room_id' => 'required|exists:rooms,id', 
        ]);

        $reporterParticipant = RoomParticipant::where('user_id', auth()->id())
            ->where('room_id', $request->room_id)
            ->firstOrFail();
       
        $offenderParticipant = RoomParticipant::where('user_id', $offenderId)
            ->where('room_id', $request->room_id)
            ->firstOrFail();

        Report::create([
            'reporter_id' => $reporterParticipant->id, 
            'offender_id' => $offenderParticipant->id, 
            'message' => $request->message,
            'Report_message' => $request->reported_message ?? 'No specific message reported',
        ]);

        return back()->with('success', 'Report submitted successfully.');
    }

    public function index()
    {
        $reports = Report::with([
            'reporterParticipant.user', 
            'offenderParticipant.user'
        ])->latest()->get();
        
        return view('reports.index', compact('reports'));
    }

   
}