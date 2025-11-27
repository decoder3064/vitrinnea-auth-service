<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregar campo JSON para múltiples países
            $table->json('allowed_countries')->nullable()->after('country');
        });

        // Migrar datos existentes: convertir country (string) a allowed_countries (array)
        \DB::table('users')->whereNotNull('country')->get()->each(function ($user) {
            \DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'allowed_countries' => json_encode([$user->country])
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('allowed_countries');
        });
    }
};
