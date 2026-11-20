<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCandidateProfileRequest;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\User;
use App\Support\CandidateProfilePresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class CandidateProfileController extends Controller
{
    public function show(Request $request): \Illuminate\Contracts\View\View
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->role === 'candidate', 403);
        $profile = $user->candidateProfile()->firstOrCreate([]);

        return view('candidate.profile', [
            'candidate' => CandidateProfilePresenter::forCandidate($user, $profile),
            'notifications' => [],
            'sidebarCounts' => ['applications' => $user->applications()->count()],
        ]);
    }

    public function update(UpdateCandidateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->role === 'candidate', 403);
        $profile = $user->candidateProfile()->firstOrCreate([]);
        $data = $request->validated();
        $oldPhotoPath = $profile->photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = strtolower($file->extension());
            $newPhotoPath = $file->storeAs(
                'candidate-profile-photos/'.$profile->getKey(),
                Str::uuid().'.'.$extension,
                'local'
            );
            if (! is_string($newPhotoPath)) {
                throw new \RuntimeException('The profile photo could not be uploaded.');
            }
        }

        $photoPath = $newPhotoPath
            ?? ($request->boolean('remove_photo') ? null : $oldPhotoPath);

        try {
            DB::transaction(function () use ($user, $profile, $data, $photoPath): void {
                $user->forceFill([
                    'name' => trim($data['first_name'].' '.$data['last_name']),
                ])->save();
                $profile->forceFill([
                    'headline' => $data['headline'] ?? null,
                    'location' => $data['location'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'portfolio_url' => $data['portfolio_url'] ?? null,
                    'summary' => $data['summary'] ?? null,
                    'availability' => $data['availability'],
                    'preferred_locations' => array_values(array_filter(
                        $data['preferred_locations'] ?? $profile->preferred_locations ?? [],
                        static fn ($location): bool => is_string($location) && $location !== ''
                    )),
                    'expected_salary_min' => $data['expected_salary_min'] ?? null,
                    'expected_salary_max' => $data['expected_salary_max'] ?? null,
                    'photo_path' => $photoPath,
                ]);
                $profile->completion = CandidateProfilePresenter::completion($profile);
                $profile->save();
            });
        } catch (Throwable $exception) {
            if ($newPhotoPath !== null && ! Storage::disk('local')->delete($newPhotoPath)) {
                throw new \RuntimeException('The profile could not be saved or its new photo could not be removed.', 0, $exception);
            }

            throw $exception;
        }

        if (($newPhotoPath !== null || $request->boolean('remove_photo'))
            && $oldPhotoPath !== null
            && Storage::disk('local')->exists($oldPhotoPath)
            && ! Storage::disk('local')->delete($oldPhotoPath)) {
            throw new \RuntimeException('The profile was saved, but the previous photo could not be removed.');
        }

        return redirect()->route('candidate.profile')
            ->with('status', 'Your profile has been updated.')
            ->with('status_type', 'success');
    }

    public function photo(Request $request): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->role === 'candidate', 403);
        $profile = $user->candidateProfile;
        abort_unless($profile?->photo_path, 404);

        return $this->privateImage($profile->photo_path);
    }

    public function photoForAdmin(User $user): StreamedResponse
    {
        abort_unless($user->role === 'candidate', 404);
        $profile = $user->candidateProfile;
        abort_unless($profile?->photo_path, 404);

        return $this->privateImage($profile->photo_path);
    }

    public function photoForEmployer(Request $request, int $id): StreamedResponse
    {
        $company = $request->attributes->get('employerCompany');
        abort_unless($company instanceof Company, 403);
        $application = JobApplication::with('candidate.candidateProfile')
            ->whereKey($id)
            ->whereHas('job', fn ($jobs) => $jobs->where('company_id', $company->id))
            ->firstOrFail();
        $profile = $application->candidate->candidateProfile;
        abort_unless($profile?->photo_path, 404);

        return $this->privateImage($profile->photo_path);
    }

    private function privateImage(string $path): StreamedResponse
    {
        $disk = Storage::disk('local');
        abort_unless($disk->exists($path), 404);
        $mime = $disk->mimeType($path);
        abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        return $disk->response($path, null, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
