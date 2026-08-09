<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions relevant to the Admission Module
        $permissions = [
            'import-admission-leads',
            'view-all-leads',
            'view-assigned-leads',
            'forward-lead',
            'direct-admit-lead',
            'assess-lead',
            'approve-reject-lead',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'parent']);

        // Assign permissions to roles
        $admin->givePermissionTo([
            'import-admission-leads',
            'view-all-leads',
            'forward-lead',
            'direct-admit-lead',
            'approve-reject-lead',
        ]);

        $teacher->givePermissionTo([
            'view-assigned-leads',
            'assess-lead',
        ]);

        // Seed a default admin user
        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@school.test'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
            ]
        );
        $adminUser->assignRole('admin');
    }
}