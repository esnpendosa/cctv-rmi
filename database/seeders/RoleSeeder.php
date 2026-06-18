<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class RoleSeeder
 * 
 * Seeds roles and permissions.
 * 
 * @package Database\Seeders
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // Cameras
            'camera.view',
            'camera.create',
            'camera.edit',
            'camera.delete',
            'camera.set_public',
            'camera.set_private',

            // Invoices
            'invoice.view',
            'invoice.create',
            'invoice.edit',
            'invoice.delete',
            'invoice.send',
            'invoice.record_payment',

            // Quotations
            'quotation.view',
            'quotation.create',
            'quotation.edit',
            'quotation.delete',

            // Inventory
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',

            // Reports
            'report.view',
            'report.export',

            // Users
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Settings
            'settings.view',
            'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Create Roles and Assign Permissions

        // Super Admin: full access
        $superAdminRole = Role::findOrCreate('Super Admin');
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin: all except user management & settings
        $adminRole = Role::findOrCreate('Admin');
        $adminRole->givePermissionTo(
            Permission::whereNotIn('name', [
                'user.view', 'user.create', 'user.edit', 'user.delete',
                'settings.view', 'settings.edit'
            ])->get()
        );

        // Technician: cameras, monitoring (view, edit, delete cameras), map, inventory view
        $technicianRole = Role::findOrCreate('Technician');
        $technicianRole->givePermissionTo([
            'camera.view',
            'camera.create',
            'camera.edit',
            'camera.delete',
            'inventory.view',
            'report.view',
        ]);

        // Finance: invoices, quotations, clients, reports
        $financeRole = Role::findOrCreate('Finance');
        $financeRole->givePermissionTo([
            'invoice.view',
            'invoice.create',
            'invoice.edit',
            'invoice.delete',
            'invoice.send',
            'invoice.record_payment',
            'quotation.view',
            'quotation.create',
            'quotation.edit',
            'quotation.delete',
            'inventory.view',
            'report.view',
            'report.export',
        ]);

        // Viewer: dashboard, monitoring (view only), map
        $viewerRole = Role::findOrCreate('Viewer');
        $viewerRole->givePermissionTo([
            'camera.view',
            'inventory.view',
            'report.view',
        ]);
    }
}
