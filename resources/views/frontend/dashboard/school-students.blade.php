@extends('frontend.layouts.school')

@section('content')

<section class="space-top space-extra-bottom" style="background-color: #f3f4f6;">
    <div class="container-fluid px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1" style="color: #111827; font-weight: 700;">Student Management</h2>
                <p class="text-muted mb-0">Monitor and manage student registrations</p>
            </div>
            <a href="{{ route('frontend.school.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Filters Section -->
        <div class="card shadow-sm rounded-4 border-0 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('frontend.school.students') }}" class="row g-3 align-items-end">
                    <!-- Grade Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Grade</label>
                        <select name="grade" class="form-select rounded-pill bg-light border-0 px-3">
                            <option value="">All Grades</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Section</label>
                        <select name="section" class="form-select rounded-pill bg-light border-0 px-3">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>{{ $section }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Gender</label>
                        <select name="gender" class="form-select rounded-pill bg-light border-0 px-3">
                            <option value="">All Genders</option>
                            <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Search Student</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill bg-light border-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control rounded-end-pill bg-light border-0" placeholder="Name..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-3 text-end">
                        <button type="submit" class="btn rounded-pill px-4 text-white me-2" style="background-color: #490D59;">
                            Apply Filters
                        </button>
                        <a href="{{ route('frontend.school.students') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Student Name</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Parent Name</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Gender</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Grade</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Section</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm rounded-circle me-3 d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background-color: #490D59;">
                                                {{ substr($student->student_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.9rem;">{{ $student->student_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-medium" style="font-size: 0.9rem;">{{ $student->user->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill bg-light text-dark border fw-normal" style="font-size: 0.8rem;">
                                            {{ $student->gender ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-dark fw-medium" style="font-size: 0.9rem;">{{ $student->grade }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-dark fw-medium" style="font-size: 0.9rem;">{{ $student->section ?? '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users-slash fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No students found matching your criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($students->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #490D59 !important;
        box-shadow: 0 0 0 0.25rem rgba(73, 13, 89, 0.1) !important;
    }
    .text-xs {
        font-size: 0.75rem !important;
    }
    .table > :not(caption) > * > * {
        padding: 1rem 1rem;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }
</style>
@endsection
