@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.school-header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-2">School Settings</h2>
                    <p class="text-muted mb-0">Manage your school's contact and profile information</p>
                </div>
                <div>
                    <a href="{{ route('frontend.school.dashboard') }}" class="vs-btn style2"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('frontend.school.settings.update') }}" method="POST">
                            @csrf
                            
                            <h5 class="mb-4 text-primary-color">Contact Information</h5>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Contact Name</label>
                                    <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $school->contact_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Contact Email</label>
                                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $school->contact_email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Contact Phone</label>
                                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $school->contact_phone) }}" required>
                                </div>
                            </div>

                            <h5 class="mb-4 text-primary-color">Address Details</h5>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $school->address ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $school->city) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">State</label>
                                    <input type="text" name="state" class="form-control" value="{{ old('state', $school->state) }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('frontend.school.dashboard') }}" class="vs-btn style2">Cancel</a>
                                <button type="submit" class="vs-btn">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .text-primary-color {
        color: #490D59;
    }
    .form-control:focus {
        border-color: #490D59;
        box-shadow: 0 0 0 0.25rem rgba(73, 13, 89, 0.1);
    }
</style>
@endsection
