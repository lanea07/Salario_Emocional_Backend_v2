<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'root',
            'email' => 'root@localhost',
            'password' => 'root@localhost',
            'dependency_id' => 1,
            'position_id' => 1,
        ]);
        $role = Role::create(['name' => 'SuperAdmin']);
        $permission = Permission::create(['name' => 'All Granted']);
        $role->givePermissionTo($permission);
        $user->syncRoles(['SuperAdmin']);
    }
}
