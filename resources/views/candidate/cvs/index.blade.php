@extends('layouts.app')
@section('title', 'My CVs')

@section('content')
<div class="page-head mb-4">
    <h1 class="page-title">My CVs</h1>
    <p class="text-muted-emp mb-0">Store your CV securely and reuse it when you apply. You choose which CV to use for each application.</p>
</div>

<div class="row g-4">
    <div class="col-lg-5 order-lg-2" id="upload">
        <div class="card-emp p-4">
            <h2 class="h5 mb-3">Upload a new CV</h2>
            <form action="{{ route('candidate.cvs.upload') }}" method="POST" enctype="multipart/form-data">
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
                <button class="btn btn-emp w-100">Save CV</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7 order-lg-1">
        <div class="card-emp">
            @forelse ($cvs as $cv)
                <div class="file-row" style="{{ $loop->first ? '' : 'border-top:1px solid var(--emp-line);' }}">
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
                    <div class="px-4 pb-3 small text-muted-emp"><i class="bi bi-hourglass-split me-1"></i>Analysis usually takes under a minute. We will notify you when it is ready.</div>
                @endif
            @empty
                @include('components.empty-state', ['icon' => 'file-earmark-text', 'title' => 'No CV yet', 'text' => 'Save a CV securely so you can reuse it when applying.'])
            @endforelse
        </div>
    </div>
</div>
@endsection
