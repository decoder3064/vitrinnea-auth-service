<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

echo "🔍 Checking for existing test users...\n\n";

// Check for admin user
$admin = User::where('email', 'admin@vitrinnea.com')->first();

if ($admin) {
    echo "✅ Admin user exists:\n";
    echo "   Email: {$admin->email}\n";
    echo "   Name: {$admin->name}\n";
    echo "   Country: {$admin->country}\n";
    echo "   Allowed Countries: " . json_encode($admin->allowed_countries) . "\n";
    echo "   Active: " . ($admin->active ? 'Yes' : 'No') . "\n";
    echo "   Roles: " . $admin->roles->pluck('name')->join(', ') . "\n\n";
    
    // Update allowed_countries if null
    if (!$admin->allowed_countries) {
        $admin->allowed_countries = ['SV', 'GT', 'CR'];
        $admin->save();
        echo "✅ Updated allowed_countries to: ['SV', 'GT', 'CR']\n\n";
    }
} else {
    echo "❌ Admin user not found. Creating...\n";
    
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@vitrinnea.com',
        'password' => Hash::make('password'),
        'user_type' => 'employee',
        'country' => 'SV',
        'allowed_countries' => ['SV', 'GT', 'CR'],
        'active' => true,
        'email_verified_at' => now(),
    ]);
    
    // Assign super_admin role if it exists
    $superAdminRole = Role::where('name', 'super_admin')->first();
    if ($superAdminRole) {
        $admin->assignRole($superAdminRole);
    }
    
    echo "✅ Admin user created!\n";
    echo "   Email: admin@vitrinnea.com\n";
    echo "   Password: password\n";
    echo "   Allowed Countries: SV, GT, CR\n\n";
}

// Create a regular test user
$testUser = User::where('email', 'test@vitrinnea.com')->first();

if ($testUser) {
    echo "✅ Test user exists:\n";
    echo "   Email: {$testUser->email}\n";
    echo "   Name: {$testUser->name}\n";
    echo "   Country: {$testUser->country}\n";
    echo "   Allowed Countries: " . json_encode($testUser->allowed_countries) . "\n";
    echo "   Active: " . ($testUser->active ? 'Yes' : 'No') . "\n\n";
    
    // Update allowed_countries if null
    if (!$testUser->allowed_countries) {
        $testUser->allowed_countries = ['SV'];
        $testUser->save();
        echo "✅ Updated allowed_countries to: ['SV']\n\n";
    }
} else {
    echo "❌ Test user not found. Creating...\n";
    
    $testUser = User::create([
        'name' => 'Test User',
        'email' => 'test@vitrinnea.com',
        'password' => Hash::make('password'),
        'user_type' => 'employee',
        'country' => 'SV',
        'allowed_countries' => ['SV'],
        'active' => true,
        'email_verified_at' => now(),
    ]);
    
    // Assign employee role if it exists
    $employeeRole = Role::where('name', 'employee')->first();
    if ($employeeRole) {
        $testUser->assignRole($employeeRole);
    }
    
    echo "✅ Test user created!\n";
    echo "   Email: test@vitrinnea.com\n";
    echo "   Password: password\n";
    echo "   Allowed Countries: SV\n\n";
}

echo "
📋 TEST CREDENTIALS:
====================

1️⃣  ADMIN (Multi-country access):
   Email: admin@vitrinnea.com
   Password: password
   Countries: SV, GT, CR

2️⃣  REGULAR USER (Single country):
   Email: test@vitrinnea.com
   Password: password
   Countries: SV

🧪 Testing Instructions:
========================
1. Go to http://localhost:3000/login
2. Select country: SV
3. Use credentials above
4. Check browser console for logs

✅ Done!
\n";
