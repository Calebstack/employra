@extends('layouts.employer-pending')

@section('title', 'Company verification')

@section('content')
    <section class="card-emp p-4 p-md-5 text-center w-100" style="max-width:640px;">
        <div class="icon-tile orange mx-auto mb-3" style="width:64px;height:64px;border-radius:18px;"><i class="bi bi-building-check fs-3"></i></div>
        @if ($company->status === 'rejected')
            <span class="badge-soft grey mb-3">Verification not approved</span>
            <h1 class="h2 text-navy mb-3">{{ $company->name }}</h1>
            <p class="text-muted-emp mb-0">Your company profile was not approved. Please contact Employra support if you need help.</p>
        @else
            <span class="badge-soft mb-3">Awaiting verification</span>
            <h1 class="h2 text-navy mb-3">{{ $company->name }}</h1>
            <p class="text-muted-emp mb-0">Your company profile is being reviewed. Company tools and job posting will be available once the Employra team verifies your company.</p>
        @endif
        <div class="small text-muted-emp mt-3"><i class="bi bi-briefcase me-1"></i>{{ $company->industry }}</div>
        <hr class="my-4">
        <p class="small text-muted-emp mb-3"><i class="bi bi-shield-check me-1"></i>Verification helps keep Employra trustworthy for candidates and employers alike.</p>
        <a href="{{ route('employer.company') }}" class="btn btn-emp">Complete company profile</a>
    </section>
@endsection
