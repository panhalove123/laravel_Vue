<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'created_at' => now(),
            ],
            [
                'name' => 'user',
                'created_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
