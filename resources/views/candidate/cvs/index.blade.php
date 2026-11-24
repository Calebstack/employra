@extends('layouts.app')
@section('title', 'My CVs')

@section('content')
<div class="page-banner compact">
    <h1 class="page-title">My CVs</h1>
    <p>Store your CV securely and reuse it when you apply. You choose which CV to use for each application.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5 order-lg-2" id="upload">
        <div class="card-emp p-4 position-sticky" style="top: 90px;">
            <h2 class="h5 mb-3"><i class="bi bi-cloud-arrow-up text-orange me-2"></i>Upload a new CV</h2>
            <form action="{{ route('candidate.cvs.upload') }}" method="POST" enctype="multipart/form-data" data-cv-upload>
                @csrf
                <label class="dropzone mb-3" for="cv-file">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <div class="fw-bold text-navy mt-2">Drag a file here or click to browse</div>
                    <div class="small text-muted-emp">PDF or DOCX, up to 5 MB</div>
                    <div class="small fw-bold text-blue mt-2" data-file-name></div>
                    <input id="cv-file" type="file" name="cv" accept=".pdf,.doc,.docx" class="visually-hidden">
                </label>
                @error('cv')<div class="text-danger small mb-3">{{ $message }}</div>@enderror
                <div class="alert alert-info small py-2"><i class="bi bi-shield-check me-1"></i>Your CV is private. Employers only see it when you apply to their job.</div>
                <div class="cv-upload-progress" data-upload-progress hidden role="status" aria-live="polite" aria-atomic="true">
                    <span class="cv-upload-progress-icon" aria-hidden="true"><i class="bi bi-file-earmark-arrow-up"></i></span>
                    <span class="cv-upload-progress-copy">
                        <strong>Uploading and preparing your CV</strong>
                        <span>This can take a little while. Keep this page open while we prepare your feedback.</span>
                    </span>
                    <span class="cv-upload-indicator" aria-hidden="true"><span></span></span>
                </div>
                <button type="submit" class="btn btn-emp w-100">
                    <span data-submit-label>Save CV</span>
                    <i class="bi bi-arrow-up-right" data-submit-icon aria-hidden="true"></i>
                    <span class="spinner-border spinner-border-sm d-none" data-submit-spinner aria-hidden="true"></span>
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7 order-lg-1">
        <div class="card-emp">
            <div class="card-head"><h2 class="h5 mb-0"><i class="bi bi-folder2-open text-orange me-2"></i>Saved CVs</h2></div>
            @forelse ($cvs as $cv)
                <div class="file-row flex-wrap" style="{{ $loop->first ? '' : 'border-top:1px solid var(--emp-line);' }}">
                    <div class="file-ico {{ strtolower($cv['type']) }}"><i class="bi bi-file-earmark-{{ $cv['type'] === 'PDF' ? 'pdf' : 'word' }}"></i></div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-navy text-truncate">{{ $cv['filename'] }}
                            @if ($cv['primary'])<span class="badge-soft ms-1">Primary</span>@endif
                        </div>
                        <div class="small text-muted-emp">{{ $cv['type'] }} · {{ $cv['size'] }} · Uploaded {{ $cv['uploaded'] }}@if($cv['used_in']) · Used in {{ $cv['used_in'] }} applications @endif</div>
                    </div>
                    @include('components.status-badge', ['status' => $cv['status']])
                    <a class="btn btn-outline-navy btn-sm" href="{{ $cv['download_url'] }}">Download</a>
                </div>
                @if ($cv['status'] === 'Failed')
                    <div class="px-4 pb-3 small text-danger"><i class="bi bi-exclamation-circle me-1"></i>We could not read this file. It looks like a scanned image. Try uploading a text-based PDF or a DOCX.</div>
                @elseif ($cv['status'] === 'Processing')
                    <div class="cv-processing-note mx-4 mb-3" role="status" aria-live="polite">
                        <span class="cv-processing-icon" aria-hidden="true"><i class="bi bi-hourglass-split"></i></span>
                        <span><strong>Your CV is saved and its analysis is underway.</strong><small>We are preparing clear, practical feedback and will notify you when it is ready.</small></span>
                    </div>
                @endif
            @empty
                @include('components.empty-state', ['icon' => 'file-earmark-text', 'title' => 'No CV yet', 'text' => 'Save a CV securely so you can reuse it when applying.'])
            @endforelse
        </div>
    </div>
</div>
@endsection
