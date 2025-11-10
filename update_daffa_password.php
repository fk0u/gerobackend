<?php
/**
 * Script to update Daffa's password to 'daffa123'
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "🔐 Updating Daffa's password...\n";

$user = User::where('email', 'daffa@gmail.com')->first();

if (!$user) {
    echo "❌ User daffa@gmail.com not found!\n";
    echo "📝 Available users:\n";
    User::all()->each(function($u) {
        echo "   - {$u->email} ({$u->role})\n";
    });
    exit(1);
}

$user->password = Hash::make('daffa123');
$user->save();

echo "✅ Password updated successfully!\n";
echo "📧 Email: daffa@gmail.com\n";
echo "🔑 New Password: daffa123\n";
echo "👤 Role: {$user->role}\n";
echo "📛 Name: {$user->name}\n";
