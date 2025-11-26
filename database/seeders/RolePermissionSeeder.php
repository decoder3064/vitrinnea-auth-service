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

            // Financial
            'view_financials',
            'manage_transfers',

            // Content
            'manage_photography',
            'manage_marketing',

            // Customer service
            'manage_customer_service',

            // Store operations
            'manage_store',
            'manage_cashier',
            'manage_dispatch',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create roles based on production enum

        // 1 - Admin (Super Admin - Full access)
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'api']);
        $admin->givePermissionTo(Permission::all());

        // 2 - User (Basic user/employee)
        $user = Role::create(['name' => 'User', 'guard_name' => 'api']);
        $user->givePermissionTo([
            'view_orders',
            'view_inventory',
        ]);

        // 3 - Gestores
        $gestores = Role::create(['name' => 'Gestores', 'guard_name' => 'api']);
        $gestores->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders',
            'view_inventory', 'edit_inventory',
            'view_users',
            'view_reports',
        ]);

        // 4 - Contabilidad
        $contabilidad = Role::create(['name' => 'Contabilidad', 'guard_name' => 'api']);
        $contabilidad->givePermissionTo([
            'view_orders',
            'view_reports', 'export_reports',
            'view_financials',
        ]);

        // 8 - Data
        $data = Role::create(['name' => 'Data', 'guard_name' => 'api']);
        $data->givePermissionTo([
            'view_orders',
            'view_reports', 'export_reports',
            'view_financials',
        ]);

        // 11 - Programadores
        $programadores = Role::create(['name' => 'Programadores', 'guard_name' => 'api']);
        $programadores->givePermissionTo(Permission::all());

        // 12 - Vendedor
        $vendedor = Role::create(['name' => 'Vendedor', 'guard_name' => 'api']);
        $vendedor->givePermissionTo([
            'view_orders', 'create_orders',
            'view_inventory',
        ]);

        // 13 - Marketing
        $marketing = Role::create(['name' => 'Marketing', 'guard_name' => 'api']);
        $marketing->givePermissionTo([
            'view_orders',
            'view_reports',
            'manage_marketing',
        ]);

        // 14 - Influencer
        $influencer = Role::create(['name' => 'Influencer', 'guard_name' => 'api']);
        $influencer->givePermissionTo([
            'view_orders',
        ]);

        // 15 - Cupones
        $cupones = Role::create(['name' => 'Cupones', 'guard_name' => 'api']);
        $cupones->givePermissionTo([
            'view_orders',
            'create_orders',
        ]);

        // 16 - Operaciones
        $operaciones = Role::create(['name' => 'Operaciones', 'guard_name' => 'api']);
        $operaciones->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders',
            'view_inventory', 'edit_inventory',
            'view_warehouse', 'manage_warehouse',
            'view_reports',
        ]);

        // 18 - Transferencias
        $transferencias = Role::create(['name' => 'Transferencias', 'guard_name' => 'api']);
        $transferencias->givePermissionTo([
            'view_orders',
            'transfer_inventory',
            'manage_transfers',
        ]);

        // 19 - Fotografia
        $fotografia = Role::create(['name' => 'Fotografia', 'guard_name' => 'api']);
        $fotografia->givePermissionTo([
            'manage_photography',
            'view_inventory',
        ]);

        // 20 - Digitacion
        $digitacion = Role::create(['name' => 'Digitacion', 'guard_name' => 'api']);
        $digitacion->givePermissionTo([
            'view_orders',
            'edit_orders',
            'view_inventory',
        ]);

        // 22 - Motorista
        $motorista = Role::create(['name' => 'Motorista', 'guard_name' => 'api']);
        $motorista->givePermissionTo([
            'view_orders',
        ]);

        // 23 - AtencionCliente
        $atencionCliente = Role::create(['name' => 'AtencionCliente', 'guard_name' => 'api']);
        $atencionCliente->givePermissionTo([
            'view_orders', 'edit_orders',
            'manage_customer_service',
        ]);

        // 24 - Store
        $store = Role::create(['name' => 'Store', 'guard_name' => 'api']);
        $store->givePermissionTo([
            'view_orders', 'create_orders',
            'view_inventory',
            'manage_store',
        ]);

        // 25 - GestorDF
        $gestorDF = Role::create(['name' => 'GestorDF', 'guard_name' => 'api']);
        $gestorDF->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders',
            'view_inventory', 'edit_inventory',
            'view_users',
            'view_reports',
        ]);

        // 26 - Cajero
        $cajero = Role::create(['name' => 'Cajero', 'guard_name' => 'api']);
        $cajero->givePermissionTo([
            'view_orders',
            'manage_cashier',
        ]);

        // 28 - VendedorTienda
        $vendedorTienda = Role::create(['name' => 'VendedorTienda', 'guard_name' => 'api']);
        $vendedorTienda->givePermissionTo([
            'view_orders', 'create_orders',
            'view_inventory',
            'manage_store',
        ]);

        // 29 - Despacho
        $despacho = Role::create(['name' => 'Despacho', 'guard_name' => 'api']);
        $despacho->givePermissionTo([
            'view_orders', 'edit_orders',
            'view_inventory',
            'manage_dispatch',
        ]);

        // 30 - Administracion
        $administracion = Role::create(['name' => 'Administracion', 'guard_name' => 'api']);
        $administracion->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders', 'delete_orders',
            'view_inventory', 'edit_inventory',
            'view_users', 'create_users', 'edit_users',
            'view_warehouse', 'manage_warehouse',
            'view_reports', 'export_reports',
            'manage_settings',
        ]);

        // 31 - Procesamiento
        $procesamiento = Role::create(['name' => 'Procesamiento', 'guard_name' => 'api']);
        $procesamiento->givePermissionTo([
            'view_orders', 'edit_orders',
            'view_inventory',
        ]);

        // 32 - Trasladar
        $trasladar = Role::create(['name' => 'Trasladar', 'guard_name' => 'api']);
        $trasladar->givePermissionTo([
            'transfer_inventory',
            'view_inventory',
        ]);

        // Create test users

        // Admin
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $adminUser->assignRole('Admin');

        // Programador
        $programadorUser = User::create([
            'name' => 'Programador',
            'email' => 'programador@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $programadorUser->assignRole('Programadores');

        // Gestor
        $gestorUser = User::create([
            'name' => 'Gestor',
            'email' => 'gestor@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $gestorUser->assignRole('Gestores');

        // Operaciones
        $operacionesUser = User::create([
            'name' => 'Operaciones',
            'email' => 'operaciones@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $operacionesUser->assignRole('Operaciones');

        // User (Basic Employee)
        $basicUser = User::create([
            'name' => 'Usuario Básico',
            'email' => 'user@vitrinnea.com',
            'password' => Hash::make('password'),
            'user_type' => 'employee',
            'country' => 'SV',
            'active' => true,
        ]);
        $basicUser->assignRole('User');

        $this->command->info('Roles, permissions, and test users created successfully!');
        $this->command->info('');
        $this->command->info('Test Users:');
        $this->command->info('  Admin: admin@vitrinnea.com / password');
        $this->command->info('  Programador: programador@vitrinnea.com / password');
        $this->command->info('  Gestor: gestor@vitrinnea.com / password');
        $this->command->info('  Operaciones: operaciones@vitrinnea.com / password');
        $this->command->info('  User: user@vitrinnea.com / password');
    }
}
