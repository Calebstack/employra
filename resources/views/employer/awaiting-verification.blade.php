@extends('layouts.employer-pending')

@section('title', 'Company verification')

@section('content')
    <section class="card-emp p-4 p-md-5 text-center" style="max-width:640px;">
        <div class="icon-tile orange mx-auto mb-3"><i class="bi bi-building-check"></i></div>
        @if ($company->status === 'rejected')
            <span class="badge-soft grey mb-3">Verification not approved</span>
            <h1 class="h2 text-navy mb-3">{{ $company->name }}</h1>
            <p class="text-muted-emp mb-0">Your company profile was not approved. Please contact Employra support if you need help.</p>
        @else
            <span class="badge-soft mb-3">Awaiting verification</span>
            <h1 class="h2 text-navy mb-3">{{ $company->name }}</h1>
            <p class="text-muted-emp mb-0">Your company profile is being reviewed. Company tools and job posting will be available once the Employra team verifies your company.</p>
        @endif
        <div class="small text-muted-emp mt-3">{{ $company->industry }}</div>
        <a href="{{ route('employer.company') }}" class="btn btn-outline-navy mt-4">Complete company profile</a>
    </section>
@endsection
