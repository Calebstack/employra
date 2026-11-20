<?php
declare(strict_types=1);

namespace App\Support;

use App\Models\CandidateProfile;
use App\Models\Cv;

class CandidateCvPresenter
{
    public static function forProfile(CandidateProfile $profile): array
    {
        return $profile->cvs()->latest()->get()->map(static function (Cv $cv): array {
            $extension = strtoupper(pathinfo($cv->original_name, PATHINFO_EXTENSION));
            $size = $cv->size_bytes >= 1048576
                ? number_format($cv->size_bytes / 1048576, 1).' MB'
                : number_format(max(1, (int) ceil($cv->size_bytes / 1024))).' KB';

            return [
                'id' => $cv->getKey(),
                'filename' => $cv->original_name,
                'type' => $extension,
                'size' => $size,
                'uploaded' => $cv->created_at?->format('j M Y') ?? 'just now',
                'status' => ucfirst($cv->status),
                'primary' => $cv->is_primary,
                'used_in' => 0,
                'download_url' => route('candidate.cvs.download', $cv->getKey()),
            ];
        })->all();
    }
}
