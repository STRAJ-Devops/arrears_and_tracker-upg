<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('officers')->insert([
            'staff_id' => 1,
            'names' => 'VFU Admin',
            'user_type' => 5,
            'username' => 'admin@vfu.com',
            'password' => Hash::make('vfu@2024'),
            'un_hashed_password' => 'vfu@2024',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
