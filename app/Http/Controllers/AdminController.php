<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Report;
use App\Models\RoomParticipant; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('is_admin', 1)->get();

        // จำนวนผู้ใช้ใหม่ใน 7 วันที่ผ่านมา
        $weeklyNew = User::where('created_at', '>=', now()->subDays(7))->count();

        // จำนวนผู้ใช้ทั้งหมด
        $allUsers = User::count();

        // Top 5 ผู้เล่น ตามเวลาการเล่นรวมจากตาราง game_stat
        if (Schema::hasTable('game_stats')) {
            $topPlayers = DB::table('game_stats')
                ->leftJoin('users', 'game_stats.user_id', '=', 'users.id')
                ->select('game_stats.user_id as user_id', 'users.name', DB::raw('SUM(game_stats.game_play_time) as total_play_time'))
                ->groupBy('game_stats.user_id', 'users.name')
                ->orderByDesc('total_play_time')
                ->limit(5)
                ->get();
        } else {
            $topPlayers = collect();
        }

        return view('admin.admindashboard', compact('admins', 'weeklyNew', 'allUsers', 'topPlayers'));
    }

    public function banuser()
    {
        $admins = User::where('is_admin', 1)->get();
        
        // Load reports with their relationships
        $reports = Report::with([
            'reporterParticipant.user',
            'offenderParticipant.user'
        ])->latest()->get();
        
        return view('admin.banuser', compact('reports', 'admins'));
    }

    // ฟังก์ชัน BAN USER โดยใช้ user_id โดยตรง
    public function ban($userId)
    {
        $user = User::findOrFail($userId);
        
        // อัปเดตสถานะผู้ใช้ให้เป็นถูกแบน (is_active = 0)
        $user->is_active = 0;
        $user->save();

        // ลบรายงานทั้งหมดที่เกี่ยวข้องกับผู้ใช้นี้ (ถ้าต้องการ)
        // หา participant IDs ของผู้ใช้นี้
        $participantIds = RoomParticipant::where('user_id', $userId)->pluck('id');
        
        // ลบรายงานที่ผู้ใช้นี้เป็น offender
        Report::whereIn('offender_id', $participantIds)->delete();

        return redirect()->route('banuser')->with('success', 'User has been banned successfully.');
    }

    // ฟังก์ชัน DISMISS REPORT
    public function dismissReport($reportId)
    {
        $report = Report::findOrFail($reportId);
        $report->delete();
        
        return redirect()->route('banuser')->with('success', 'Report has been dismissed.');
    }

    // ฟังก์ชัน UNBAN
    public function unbanUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_active = 1;
        $user->save();

        return redirect()->route('banuser')->with('success', 'User has been unbanned successfully.');
    }
}