<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->string('photo_path', 255)->nullable();
        });

        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('availability', 30)->nullable();
            $table->unsignedInteger('expected_salary_min')->nullable();
            $table->unsignedInteger('expected_salary_max')->nullable();
        });

        Schema::create('job_application_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30);
            $table->string('actor_type', 20);
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
            $table->index(['job_application_id', 'created_at'], 'application_status_history_lookup');
        });

        Schema::create('job_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('format', 20);
            $table->text('meeting_details')->nullable();
            $table->text('candidate_message')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->string('candidate_response', 30)->default('pending');
            $table->text('response_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'scheduled_at']);
        });

        foreach (DB::table('job_applications')->get() as $application) {
            DB::table('job_application_status_histories')->insert([
                'job_application_id' => $application->id,
                'status' => $application->status,
                'actor_type' => 'system',
                'actor_id' => null,
                'message' => 'Application status recorded during system setup.',
                'created_at' => $application->updated_at ?? $application->created_at,
                'updated_at' => $application->updated_at ?? $application->created_at,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_interviews');
        Schema::dropIfExists('job_application_status_histories');

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['availability', 'expected_salary_min', 'expected_salary_max']);
        });

        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
