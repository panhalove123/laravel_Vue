<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleUser = [
            [
                'role_id' => 1, // admin
                'user_id' => 1,
                'created_at' => now(),
            ],
            [
                'role_id' => 2, // user
                'user_id' => 2,
                'created_at' => now(),
            ],
        ];

        DB::table('role_user')->insert($roleUser);
    }
}
