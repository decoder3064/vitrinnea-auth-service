<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📦 Creating groups...');

        $groups = [
            [
                'name' => 'admin',
                'display_name' => 'Administradores',
                'description' => 'Grupo de administradores del sistema',
                'active' => true,
            ],
            [
                'name' => 'customer_service',
                'display_name' => 'Atención al Cliente',
                'description' => 'Grupo de atención al cliente',
                'active' => true,
            ],
            [
                'name' => 'it',
                'display_name' => 'IT',
                'description' => 'Grupo de tecnología y programadores',
                'active' => true,
            ],
            [
                'name' => 'operations',
                'display_name' => 'Operaciones',
                'description' => 'Grupo de operaciones y logística',
                'active' => true,
            ],
            [
                'name' => 'sales',
                'display_name' => 'Ventas',
                'description' => 'Grupo de ventas y comercial',
                'active' => true,
            ],
            [
                'name' => 'warehouse',
                'display_name' => 'Bodega',
                'description' => 'Grupo de gestión de bodega',
                'active' => true,
            ],
            [
                'name' => 'finance',
                'display_name' => 'Finanzas',
                'description' => 'Grupo de contabilidad y finanzas',
                'active' => true,
            ],
            [
                'name' => 'marketing',
                'display_name' => 'Marketing',
                'description' => 'Grupo de marketing y contenido',
                'active' => true,
            ],
        ];

        foreach ($groups as $groupData) {
            Group::firstOrCreate(
                ['name' => $groupData['name']],
                $groupData
            );
        }

        $this->command->info('✅ ' . count($groups) . ' groups created successfully!');

        // Asignar usuarios a grupos
        $this->assignUsersToGroups();
    }

    private function assignUsersToGroups(): void
    {
        $this->command->info('👥 Assigning users to groups...');

        // Admin a grupo admin e IT
        $admin = User::where('email', 'admin@vitrinnea.com')->first();
        if ($admin) {
            $admin->groups()->sync([
                Group::where('name', 'admin')->first()?->id,
                Group::where('name', 'it')->first()?->id,
            ]);
        }

        // Programador a grupo IT
        $programador = User::where('email', 'programador@vitrinnea.com')->first();
        if ($programador) {
            $programador->groups()->sync([
                Group::where('name', 'it')->first()?->id,
            ]);
        }

        // Operaciones a grupo operations
        $operaciones = User::where('email', 'operaciones@vitrinnea.com')->first();
        if ($operaciones) {
            $operaciones->groups()->sync([
                Group::where('name', 'operations')->first()?->id,
            ]);
        }

        // Atención al Cliente a grupo customer_service
        $atencionCliente = User::where('email', 'atencion@vitrinnea.com')->first();
        if ($atencionCliente) {
            $atencionCliente->groups()->sync([
                Group::where('name', 'customer_service')->first()?->id,
            ]);
        }

        // Administración a grupo admin
        $administracion = User::where('email', 'administracion@vitrinnea.com')->first();
        if ($administracion) {
            $administracion->groups()->sync([
                Group::where('name', 'admin')->first()?->id,
                Group::where('name', 'operations')->first()?->id,
            ]);
        }

        $this->command->info('✅ Users assigned to groups!');
    }
}