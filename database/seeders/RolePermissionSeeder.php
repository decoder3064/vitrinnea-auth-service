<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Order permissions
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            
            // Inventory permissions
            'view_inventory',
            'edit_inventory',
            'transfer_inventory',
            
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Warehouse
            'view_warehouse',
            'manage_warehouse',
            
            // Reports
            'view_reports',
            'export_reports',
            
            // Settings
            'manage_settings',
            'manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        
        // Super Admin - Full access
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Country Admins
        $adminSV = Role::create(['name' => 'admin_sv']);
        $adminSV->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders',
            'view_inventory', 'edit_inventory',
            'view_users', 'create_users', 'edit_users',
            'view_warehouse', 'manage_warehouse',
            'view_reports', 'export_reports',
        ]);

        $adminGT = Role::create(['name' => 'admin_gt']);
        $adminGT->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders',
            'view_inventory', 'edit_inventory',
            'view_users', 'create_users', 'edit_users',
            'view_warehouse', 'manage_warehouse',
            'view_reports', 'export_reports',
        ]);

        // Warehouse Managers
        $warehouseManagerSV = Role::create(['name' => 'warehouse_manager_sv']);
        $warehouseManagerSV->givePermissionTo([
            'view_orders',
            'view_inventory', 'edit_inventory', 'transfer_inventory',
            'view_warehouse', 'manage_warehouse',
            'view_reports',
        ]);

        $warehouseManagerGT = Role::create(['name' => 'warehouse_manager_gt']);
        $warehouseManagerGT->givePermissionTo([
            'view_orders',
            'view_inventory', 'edit_inventory', 'transfer_inventory',
            'view_warehouse', 'manage_warehouse',
            'view_reports',
        ]);

        // Operations Staff
        $operations = Role::create(['name' => 'operations']);
        $operations->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders',
            'view_inventory',
            'view_reports',
        ]);

        // Basic Employee
        $employee = Role::create(['name' => 'employee']);
        $employee->givePermissionTo([
            'view_orders',
            'view_inventory',
        ]);

        // Create test users
        
        // Super Admin
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $superAdminUser->assignRole('super_admin');

        // El Salvador Admin
        $adminSVUser = User::create([
            'name' => 'Admin El Salvador',
            'email' => 'admin.sv@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $adminSVUser->assignRole('admin_sv');

        // Guatemala Admin
        $adminGTUser = User::create([
            'name' => 'Admin Guatemala',
            'email' => 'admin.gt@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'GT',
            'active' => true,
        ]);
        $adminGTUser->assignRole('admin_gt');

        // Warehouse Manager SV
        $warehouseUser = User::create([
            'name' => 'Warehouse Manager SV',
            'email' => 'warehouse.sv@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $warehouseUser->assignRole('warehouse_manager_sv');

        // Regular Employee
        $employeeUser = User::create([
            'name' => 'John Doe',
            'email' => 'employee@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $employeeUser->assignRole('employee');

        $this->command->info('Roles, permissions, and test users created successfully!');
        $this->command->info('');
        $this->command->info('Test Users:');
        $this->command->info('  Super Admin: admin@vitrinnea.com / password');
        $this->command->info('  Admin SV: admin.sv@vitrinnea.com / password');
        $this->command->info('  Admin GT: admin.gt@vitrinnea.com / password');
        $this->command->info('  Warehouse SV: warehouse.sv@vitrinnea.com / password');
        $this->command->info('  Employee: employee@vitrinnea.com / password');
    }
}
