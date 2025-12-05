<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role
                            {email : The email of the user}
                            {role : The role name to assign}
                            {--sync : Sync roles (remove all other roles)}
                            {--remove : Remove the role instead of adding it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign or remove roles from a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');
        $sync = $this->option('sync');
        $remove = $this->option('remove');

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        // Verify role exists
        $role = Role::where('name', $roleName)->where('guard_name', 'api')->first();

        if (!$role) {
            $this->error("❌ Role '{$roleName}' not found.");
            $this->newLine();
            $this->info('Available roles:');

            $roles = Role::where('guard_name', 'api')->pluck('name')->toArray();
            foreach ($roles as $availableRole) {
                $this->line("  • {$availableRole}");
            }

            return Command::FAILURE;
        }

        // Show current roles
        $currentRoles = $user->roles->pluck('name')->toArray();
        $this->info("Current roles: " . (empty($currentRoles) ? 'None' : implode(', ', $currentRoles)));

        // Perform action
        if ($remove) {
            $user->removeRole($roleName);
            $this->success("✅ Role '{$roleName}' removed from user '{$user->name}' ({$email})");
        } elseif ($sync) {
            $user->syncRoles([$roleName]);
            $this->success("✅ User '{$user->name}' ({$email}) now has ONLY the role '{$roleName}'");
        } else {
            $user->assignRole($roleName);
            $this->success("✅ Role '{$roleName}' assigned to user '{$user->name}' ({$email})");
        }

        // Show new roles
        $newRoles = $user->fresh()->roles->pluck('name')->toArray();
        $this->info("New roles: " . implode(', ', $newRoles));

        return Command::SUCCESS;
    }
}
