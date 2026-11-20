<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\CandidateProfile;
use App\Models\Cv;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PrivateCvStorage
{
    public function store(CandidateProfile $profile, UploadedFile $file): Cv
    {
        $sha256 = hash_file('sha256', $file->getRealPath());
        $mime = $file->getMimeType();
        $size = $file->getSize();

        if (! is_string($sha256) || ! is_string($mime) || ! is_int($size)) {
            throw new RuntimeException('The uploaded CV could not be inspected.');
        }

        $existing = $profile->cvs()->where('sha256', $sha256)->first();
        if ($existing) {
            return $existing;
        }

        $extension = strtolower($file->extension());
        if (! in_array($extension, ['pdf', 'docx'], true)) {
            throw new RuntimeException('Only PDF and DOCX CVs can be stored.');
        }

        $originalName = basename(str_replace('\\', '/', $file->getClientOriginalName()));
        $originalName = preg_replace('/[\x00-\x1F\x7F]/', '', $originalName) ?: '';
        $originalName = Str::limit(trim($originalName), 255, '');
        if ($originalName === '') {
            $originalName = 'Resume.'.$extension;
        }

        $path = $file->storeAs('cvs/'.$profile->getKey(), Str::uuid().'.'.$extension, 'local');
        if (! is_string($path)) {
            throw new RuntimeException('The CV could not be stored. Please try again.');
        }

        try {
            return DB::transaction(function () use ($profile, $originalName, $path, $mime, $size, $sha256): Cv {
                $isPrimary = ! $profile->cvs()->where('is_primary', true)->exists();

                return $profile->cvs()->create([
                    'original_name' => $originalName,
                    'stored_path' => $path,
                    'mime' => $mime,
                    'size_bytes' => $size,
                    'sha256' => $sha256,
                    'status' => 'uploaded',
                    'is_primary' => $isPrimary,
                ]);
            });
        } catch (Throwable $exception) {
            if (! Storage::disk('local')->delete($path)) {
                throw new RuntimeException('The CV could not be saved or safely removed after the failure.', 0, $exception);
            }

            throw $exception;
        }
    }
}
