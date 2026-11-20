<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('website', 255)->nullable();
            $table->string('location', 160)->nullable();
            $table->string('company_size', 60)->nullable();
            $table->text('description')->nullable();
            $table->string('registration_number', 100)->nullable();
            $table->string('logo_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'location',
                'company_size',
                'description',
                'registration_number',
                'logo_path',
            ]);
        });
    }
};
