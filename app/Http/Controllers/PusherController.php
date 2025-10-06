<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\pusherbroadcast;

class PusherController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function broadcast(Request $request)
    {
        broadcast(new pusherbroadcast($request->get('message')))->toOthers();
        return view('broadcast',['message' => $request->get('message')]);
    }

    public function receive(Request $request)
    {
        $data = $request->input('data');
        // Here you would typically handle the received data
        // For demonstration, we'll just return the data back
        return response()->json(['message' => 'Data received', 'data' => $data]);
    }
}
