<?php

namespace App\Support;

use App\Models\JobApplication;
use App\Models\JobInterview;

class JobApplicationPresenter
{
    public static function forCandidate(JobApplication $application): array
    {
        $application->loadMissing(['job.company', 'cv', 'statusHistory', 'interviews']);
        $job = JobPresenter::forPublic($application->job);
        $history = $application->statusHistory;
        $scheduledInterviews = $application->interviews
            ->where('status', 'scheduled')
            ->sortBy('scheduled_at');
        $interview = $scheduledInterviews->first(fn (JobInterview $item) => $item->scheduled_at?->isFuture())
            ?? $scheduledInterviews->last();

        return [
            'id' => $application->id,
            'job_title' => $job['title'],
            'company' => $job['company'],
            'company_color' => $job['company_color'],
            'company_initials' => $job['company_initials'],
            'location' => $job['location'],
            'mode' => $job['mode'],
            'type' => $job['type'],
            'job_slug' => $job['slug'],
            'status' => self::statusLabel($application->status),
            'submitted' => $application->created_at?->format('j M Y') ?? '',
            'updated' => $application->updated_at?->diffForHumans() ?? '',
            'closed_note' => $application->status === 'rejected'
                ? ($history->last()?->message ?: 'The company has decided not to move forward with this application.')
                : 'You withdrew this application.',
            'interview' => $interview ? self::interviewForCandidate($interview, $application) : null,
            'timeline' => $history->values()->map(fn ($entry, $index) => [
                'stage' => self::statusLabel($entry->status),
                'date' => $entry->created_at?->format('j M Y, H:i') ?? '',
                'note' => $entry->message,
                'current' => $index === $history->count() - 1,
            ])->values()->all(),
            'cv' => $application->cv?->original_name ?? 'No CV selected',
            'salary' => self::salaryRange($application->expected_salary_min, $application->expected_salary_max),
            'match' => null,
            'is_closed' => in_array($application->status, ['rejected', 'withdrawn'], true),
        ];
    }

    public static function forCompany(JobApplication $application): array
    {
        $application->loadMissing(['candidate.candidateProfile', 'job', 'cv', 'interviews']);
        $candidate = $application->candidate;
        $profile = $candidate->candidateProfile;
        $photoUrl = $profile?->photo_path ? route('employer.applicants.photo', $application) : null;

        return [
            'id' => $application->id,
            'name' => $candidate->name,
            'email' => $candidate->email,
            'headline' => $profile?->headline ?: 'Candidate',
            'location' => $profile?->location ?: 'Not specified',
            'phone' => $profile?->phone ?: 'Not provided',
            'portfolio_url' => $profile?->portfolio_url,
            'photo_url' => $photoUrl,
            'summary' => $profile?->summary ?: '',
            'cover_note' => $application->cover_note,
            'availability' => CandidateProfilePresenter::availabilityLabel($application->availability),
            'salary' => self::salaryRange($application->expected_salary_min, $application->expected_salary_max),
            'preferred_roles' => implode(', ', $profile?->preferred_roles ?? []),
            'preferred_locations' => implode(', ', $profile?->preferred_locations ?? []),
            'preferred_work_modes' => implode(', ', $profile?->preferred_work_modes ?? []),
            'cv' => $application->cv?->original_name,
            'cv_download_url' => $application->cv ? route('employer.applicants.cv', $application) : null,
            'status' => $application->status === 'submitted' ? 'New' : self::statusLabel($application->status),
            'applied' => $application->created_at?->diffForHumans() ?? '',
            'submitted_at' => $application->created_at?->format('j M Y') ?? '',
            'job_title' => $application->job->title,
            'interviews' => $application->interviews->map(fn (JobInterview $interview) => [
                'id' => $interview->id,
                'date' => $interview->scheduled_at?->format('D, j M Y') ?? '',
                'time' => $interview->scheduled_at?->format('g:i A') ?? '',
                'mode' => self::interviewFormatLabel($interview->format),
                'details' => $interview->meeting_details,
                'message' => $interview->candidate_message,
                'response' => match ($interview->candidate_response) {
                    'confirmed' => 'Confirmed',
                    'reschedule_requested' => 'Reschedule requested',
                    default => 'Awaiting response',
                },
                'response_note' => $interview->response_note,
            ])->all(),
        ];
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'submitted' => 'Submitted',
            'reviewing' => 'Reviewing',
            'shortlisted' => 'Shortlisted',
            'interview' => 'Interview',
            'offer' => 'Offer',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            default => 'Submitted',
        };
    }

    private static function salaryRange(?int $minimum, ?int $maximum): string
    {
        if ($minimum === null && $maximum === null) {
            return 'Not provided';
        }

        return 'NGN '.($minimum === null ? 'Not specified' : number_format($minimum))
            .' - NGN '.($maximum === null ? 'Not specified' : number_format($maximum)).' / month';
    }

    private static function interviewForCandidate(JobInterview $interview, JobApplication $application): array
    {
        return [
            'id' => $interview->id,
            'date' => $interview->scheduled_at?->format('l, j F Y') ?? '',
            'time' => $interview->scheduled_at?->format('g:i A') ?? '',
            'mode' => self::interviewFormatLabel($interview->format),
            'where' => $interview->meeting_details ?: 'The company will share the details directly.',
            'with' => $application->job->company->contact_name ?: $application->job->company->name,
            'notes' => $interview->candidate_message ?: 'No additional notes.',
            'response' => $interview->candidate_response,
            'response_note' => $interview->response_note,
            'respondable' => $interview->status === 'scheduled' && $interview->scheduled_at?->isFuture(),
        ];
    }

    private static function interviewFormatLabel(string $format): string
    {
        return match ($format) {
            'video' => 'Video call',
            'phone' => 'Phone call',
            'in_person' => 'In person',
            default => 'Interview',
        };
    }
}
