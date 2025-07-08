<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionRole = [];

        // Assign all permissions to admin (role_id = 1)
        for ($i = 1; $i <= 15; $i++) {
            $permissionRole[] = [
                'role_id' => 1, // admin
                'permission_id' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Assign only user-related permissions to user (role_id = 2)
        $userPermissions = [2, 3, 4, 5]; // users-view, users-create, users-edit, users-delete
        foreach ($userPermissions as $pid) {
            $permissionRole[] = [
                'role_id' => 2, // user
                'permission_id' => $pid,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('permission_role')->insert($permissionRole);
    }
}
