<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
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
                'description' => 'Grupo de tecnología',
                'active' => true,
            ],
        ];

        foreach ($groups as $group) {
            Group::firstOrCreate(
                ['name' => $group['name']],
                $group
            );
        }

        $this->command->info('✓ Groups created successfully!');
    }
}