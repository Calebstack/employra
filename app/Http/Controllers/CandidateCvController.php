<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCvRequest;
use App\Services\PrivateCvStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateCvController extends Controller
{
    public function store(StoreCvRequest $request, PrivateCvStorage $cvStorage): RedirectResponse
    {
        $profile = $request->user()->candidateProfile()->firstOrCreate([]);
        $cvStorage->store($profile, $request->file('cv'));

        return redirect()->route('candidate.cvs')
            ->with('status', 'Your CV has been saved securely.')
            ->with('status_type', 'success');
    }

    public function download(Request $request, int $cv): StreamedResponse
    {
        abort_unless($request->user()?->role === 'candidate', 403);

        $profile = $request->user()->candidateProfile;
        abort_unless($profile, 404);

        $document = $profile->cvs()->findOrFail($cv);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($document->stored_path), 404);

        return $disk->download($document->stored_path, $document->original_name, [
            'Content-Type' => $document->mime,
        ]);
    }
}
