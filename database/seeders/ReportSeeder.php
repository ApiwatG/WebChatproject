<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reports')->insert([
            'reporter_id' => 1,
            'offender_id' => 2,
            'message' => 'this is so gay', // ข้อความที่ถูกรายงาน
            'Report_message' => 'wehat', // ข้อความรายงาน/เหตุผล
            'created_at' => '2025-10-06 07:28:07', // กำหนดเวลาให้ตรงกับภาพ
            'updated_at' => '2025-10-06 07:28:07',
        ]);
    }
}