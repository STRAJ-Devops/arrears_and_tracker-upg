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
            'staff_id' => 110611,
            'names' => 'Edmond Angwala',
            'user_type' => 2,
            'username' => '1106',
            'password' => Hash::make('1106'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('officers')->insert([
            'staff_id' => 100511,
            'names' => 'VFU Admin',
            'user_type' => 1,
            'username' => 'admin@vfu.com',
            'password' => Hash::make('vfu@2024'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
