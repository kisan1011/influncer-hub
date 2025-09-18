<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        Role::insert([
            [
                'id' => 1,
                'name' => Role::ROLE_ADMIN,
                'slug' => Str::slug(Role::ROLE_ADMIN),
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => Role::ROLE_INFLUENCER,
                'slug' => Str::slug(Role::ROLE_INFLUENCER),
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'name' => Role::ROLE_BUSINESS,
                'slug' => Str::slug(Role::ROLE_BUSINESS),
                'created_at' => now(),
            ]
        ]);
    }
}
