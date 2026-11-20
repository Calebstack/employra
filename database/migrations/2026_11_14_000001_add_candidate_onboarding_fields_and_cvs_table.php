<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->string('headline', 160)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('location', 120)->nullable();
            $table->text('summary')->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->json('preferred_roles')->nullable();
            $table->json('preferred_work_modes')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->unsignedInteger('expected_salary_min')->nullable();
            $table->unsignedInteger('expected_salary_max')->nullable();
            $table->string('availability', 30)->nullable();
            $table->string('visibility', 30)->default('employers_i_apply_to');
            $table->unsignedTinyInteger('completion')->default(0);
            $table->timestamp('onboarding_completed_at')->nullable();
        });

        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_profile_id')->constrained()->cascadeOnDelete();
            $table->string('original_name', 255);
            $table->string('stored_path', 255);
            $table->string('mime', 100);
            $table->unsignedInteger('size_bytes');
            $table->string('sha256', 64)->index();
            $table->string('status', 20)->default('uploaded');
            $table->boolean('is_primary')->default(false);
            $table->string('failure_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['candidate_profile_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs');

        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'headline',
                'phone',
                'location',
                'summary',
                'portfolio_url',
                'preferred_roles',
                'preferred_work_modes',
                'preferred_locations',
                'expected_salary_min',
                'expected_salary_max',
                'availability',
                'visibility',
                'completion',
                'onboarding_completed_at',
            ]);
        });
    }
};
