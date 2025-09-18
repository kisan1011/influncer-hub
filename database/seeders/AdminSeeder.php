<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminCheck = User::where('role_id',User::ROLE_ADMIN)->first();
        if(!$adminCheck){
            User::insert([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make(12345678), //Password: 12345678
                'role_id' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
                'created_at' => now(),
            ]);
        }
    }
}
