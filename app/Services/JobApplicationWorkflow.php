<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;

class JobApplicationWorkflow
{
    private const TRANSITIONS = [
        'submitted' => ['reviewing', 'shortlisted', 'interview', 'offer', 'rejected', 'withdrawn'],
        'reviewing' => ['shortlisted', 'interview', 'offer', 'rejected', 'withdrawn'],
        'shortlisted' => ['reviewing', 'interview', 'offer', 'rejected', 'withdrawn'],
        'interview' => ['reviewing', 'shortlisted', 'offer', 'rejected', 'withdrawn'],
        'offer' => ['withdrawn'],
        'rejected' => [],
        'withdrawn' => [],
    ];

    public function transition(
        JobApplication $application,
        string $status,
        string $actorType,
        ?int $actorId,
        ?string $message = null,
    ): void {
        DB::transaction(function () use ($application, $status, $actorType, $actorId, $message): void {
            $lockedApplication = JobApplication::query()->lockForUpdate()->findOrFail($application->getKey());
            abort_unless(in_array($status, self::TRANSITIONS[$lockedApplication->status] ?? [], true), 409);

            $lockedApplication->forceFill(['status' => $status])->save();
            $lockedApplication->statusHistory()->create([
                'status' => $status,
                'actor_type' => $actorType,
                'actor_id' => $actorId,
                'message' => $message,
            ]);
            $application->setAttribute('status', $status);
        });
    }

    public function recordSubmission(JobApplication $application, int $candidateId): void
    {
        $application->statusHistory()->create([
            'status' => 'submitted',
            'actor_type' => 'candidate',
            'actor_id' => $candidateId,
            'message' => 'Your application was submitted.',
        ]);
    }
}
