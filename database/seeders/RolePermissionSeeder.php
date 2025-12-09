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
        $this->command->info('👤 Creating test users...');

        $testUsers = [
            // Super Admin - Acceso a todos los países
            [
                'name' => 'Super Admin',
                'email' => 'admin@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV', 'GT', 'CR', 'HN', 'NI', 'PA'],
                'active' => true,
                'role' => 'Admin',
            ],

            // Programador - Acceso a todos los países
            [
                'name' => 'Desarrollador Principal',
                'email' => 'programador@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV', 'GT', 'CR', 'HN', 'NI', 'PA'],
                'active' => true,
                'role' => 'Programadores',
            ],

            // Administración - Múltiples países
            [
                'name' => 'Administrador Regional',
                'email' => 'administracion@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV', 'GT', 'CR'],
                'active' => true,
                'role' => 'Administracion',
            ],

            // Gestores
            [
                'name' => 'Gestor SV',
                'email' => 'gestor@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV'],
                'active' => true,
                'role' => 'Gestores',
            ],

            // Operaciones
            [
                'name' => 'Operaciones SV',
                'email' => 'operaciones@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV', 'GT'],
                'active' => true,
                'role' => 'Operaciones',
            ],

            // Atención al Cliente
            [
                'name' => 'Atención al Cliente',
                'email' => 'atencion@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV', 'GT'],
                'active' => true,
                'role' => 'AtencionCliente',
            ],

            // Vendedor
            [
                'name' => 'Vendedor SV',
                'email' => 'vendedor@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV'],
                'active' => true,
                'role' => 'Vendedor',
            ],

            // Contabilidad
            [
                'name' => 'Contador Regional',
                'email' => 'contabilidad@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV', 'GT', 'CR'],
                'active' => true,
                'role' => 'Contabilidad',
            ],

            // Despacho
            [
                'name' => 'Encargado Despacho',
                'email' => 'despacho@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV'],
                'active' => true,
                'role' => 'Despacho',
            ],

            // Marketing
            [
                'name' => 'Marketing Manager',
                'email' => 'marketing@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV', 'GT', 'CR'],
                'active' => true,
                'role' => 'Marketing',
            ],

            // Cajero
            [
                'name' => 'Cajero Principal',
                'email' => 'cajero@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV'],
                'active' => true,
                'role' => 'Cajero',
            ],

            // Usuario Básico
            [
                'name' => 'Usuario Básico',
                'email' => 'user@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'SV',
                'allowed_countries' => ['SV'],
                'active' => true,
                'role' => 'User',
            ],

            // Usuarios por país específico

            // Guatemala
            [
                'name' => 'Admin Guatemala',
                'email' => 'admin.gt@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'GT',
                'allowed_countries' => ['GT'],
                'active' => true,
                'role' => 'Administracion',
            ],

            // Costa Rica
            [
                'name' => 'Admin Costa Rica',
                'email' => 'admin.cr@vitrinnea.com',
                'password' => Hash::make('password'),
                'user_type' => 'employee',
                'country' => 'CR',
                'allowed_countries' => ['CR'],
                'active' => true,
                'role' => 'Administracion',
            ],
        ];

        foreach ($testUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::create($userData);
            $user->assignRole($role);
        }

        $this->command->info('✅ ' . count($testUsers) . ' test users created successfully!');
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📋 TEST USERS CREDENTIALS (All use password: password)');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();

        $this->command->info('🔑 ADMINISTRATORS:');
        $this->command->info('  • admin@vitrinnea.com (Admin - All countries)');
        $this->command->info('  • programador@vitrinnea.com (Programadores - All countries)');
        $this->command->info('  • administracion@vitrinnea.com (Administracion - SV, GT, CR)');
        $this->command->newLine();

        $this->command->info('👥 OPERATIONS:');
        $this->command->info('  • operaciones@vitrinnea.com (Operaciones - SV, GT)');
        $this->command->info('  • gestor@vitrinnea.com (Gestores - SV)');
        $this->command->info('  • despacho@vitrinnea.com (Despacho - SV)');
        $this->command->newLine();

        $this->command->info('💼 SALES & SERVICE:');
        $this->command->info('  • vendedor@vitrinnea.com (Vendedor - SV)');
        $this->command->info('  • atencion@vitrinnea.com (AtencionCliente - SV, GT)');
        $this->command->info('  • cajero@vitrinnea.com (Cajero - SV)');
        $this->command->newLine();

        $this->command->info('📊 SUPPORT:');
        $this->command->info('  • contabilidad@vitrinnea.com (Contabilidad - SV, GT, CR)');
        $this->command->info('  • marketing@vitrinnea.com (Marketing - SV, GT, CR)');
        $this->command->newLine();

        $this->command->info('🌎 COUNTRY SPECIFIC:');
        $this->command->info('  • admin.gt@vitrinnea.com (Administracion - GT only)');
        $this->command->info('  • admin.cr@vitrinnea.com (Administracion - CR only)');
        $this->command->newLine();

        $this->command->info('👤 BASIC:');
        $this->command->info('  • user@vitrinnea.com (User - SV only)');
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
