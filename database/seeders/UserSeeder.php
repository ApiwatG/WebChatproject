<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /** 
     * Run the database seeds.
     */
    public function run(): void
    {
         User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com', // อีเมลสำหรับล็อกอิน
            'password' => Hash::make('admin1234'), // รหัสผ่านที่เข้ารหัสแล้ว
            'is_admin' => 1,

            
        ]);
        User::create([
            'name' => 'test',
            'email' => 'test@gmail.com', // อีเมลสำหรับล็อกอิน
            'password' => Hash::make('test1234'), // รหัสผ่านที่เข้ารหัสแล้ว
            'is_active' => 0, 
        ]);
        User::create([
            'name' => 'General User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
    }
}
