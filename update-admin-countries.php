<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔄 Updating admin user allowed countries...\n\n";

$admin = User::where('email', 'admin@vitrinnea.com')->first();

if ($admin) {
    $admin->allowed_countries = ['SV', 'GT', 'CR'];
    $admin->save();
    
    echo "✅ Admin user updated!\n";
    echo "   Email: {$admin->email}\n";
    echo "   Allowed Countries: " . implode(', ', $admin->allowed_countries) . "\n";
} else {
    echo "❌ Admin user not found\n";
}

echo "\n📋 UPDATED TEST CREDENTIALS:\n";
echo "====================\n\n";
echo "1️⃣  ADMIN (Multi-country access):\n";
echo "   Email: admin@vitrinnea.com\n";
echo "   Password: password\n";
echo "   Countries: SV, GT, CR\n\n";
echo "2️⃣  REGULAR USER (Single country):\n";
echo "   Email: test@vitrinnea.com\n";
echo "   Password: password\n";
echo "   Countries: SV\n\n";
echo "✅ Done!\n";
