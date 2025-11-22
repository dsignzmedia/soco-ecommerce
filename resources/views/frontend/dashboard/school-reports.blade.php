@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h3 mb-2">Reports & Analytics</h2>
                        <p class="text-muted mb-0">Generate comprehensive reports with visual analytics</p>
                    </div>
                    <div>
                        <a href="{{ route('frontend.school.dashboard') }}" class="vs-btn btn-sm">
                            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Filters Section -->
            <div class="col-lg-4">
                <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-filter me-2" style="color: #490D59;"></i>Apply Filters
                        </h5>
                        <form id="reportFilterForm" action="{{ route('frontend.school.generate-report') }}" method="POST">
                            @csrf
                            
                            <!-- Date Filter -->
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ session('report_filters.date') ?? '' }}">
                            </div>

                            <!-- Month Filter -->
                            <div class="mb-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-select" id="month" name="month">
                                    <option value="">Select Month</option>
                                    <option value="1" {{ session('report_filters.month') == '1' ? 'selected' : '' }}>January</option>
                                    <option value="2" {{ session('report_filters.month') == '2' ? 'selected' : '' }}>February</option>
                                    <option value="3" {{ session('report_filters.month') == '3' ? 'selected' : '' }}>March</option>
                                    <option value="4" {{ session('report_filters.month') == '4' ? 'selected' : '' }}>April</option>
                                    <option value="5" {{ session('report_filters.month') == '5' ? 'selected' : '' }}>May</option>
                                    <option value="6" {{ session('report_filters.month') == '6' ? 'selected' : '' }}>June</option>
                                    <option value="7" {{ session('report_filters.month') == '7' ? 'selected' : '' }}>July</option>
                                    <option value="8" {{ session('report_filters.month') == '8' ? 'selected' : '' }}>August</option>
                                    <option value="9" {{ session('report_filters.month') == '9' ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ session('report_filters.month') == '10' ? 'selected' : '' }}>October</option>
                                    <option value="11" {{ session('report_filters.month') == '11' ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ session('report_filters.month') == '12' ? 'selected' : '' }}>December</option>
                                </select>
                            </div>

                            <!-- Year Filter -->
                            <div class="mb-3">
                                <label for="year" class="form-label">Year</label>
                                <select class="form-select" id="year" name="year">
                                    <option value="">Select Year</option>
                                    @for($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{ $y }}" {{ session('report_filters.year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Grade Filter -->
                            <div class="mb-3">
                                <label for="grade" class="form-label">Grade</label>
                                <select class="form-select" id="grade" name="grade">
                                    <option value="">All Grades</option>
                                    <option value="LKG" {{ session('report_filters.grade') == 'LKG' ? 'selected' : '' }}>LKG</option>
                                    <option value="UKG" {{ session('report_filters.grade') == 'UKG' ? 'selected' : '' }}>UKG</option>
                                    @for($g = 1; $g <= 12; $g++)
                                        <option value="Grade {{ $g }}" {{ session('report_filters.grade') == "Grade $g" ? 'selected' : '' }}>Grade {{ $g }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Product Filter -->
                            <div class="mb-3">
                                <label for="product" class="form-label">Product</label>
                                <select class="form-select" id="product" name="product">
                                    <option value="">All Products</option>
                                    <option value="School Shirt" {{ session('report_filters.product') == 'School Shirt' ? 'selected' : '' }}>School Shirt</option>
                                    <option value="School Pants" {{ session('report_filters.product') == 'School Pants' ? 'selected' : '' }}>School Pants</option>
                                    <option value="School Skirt" {{ session('report_filters.product') == 'School Skirt' ? 'selected' : '' }}>School Skirt</option>
                                    <option value="School Tie" {{ session('report_filters.product') == 'School Tie' ? 'selected' : '' }}>School Tie</option>
                                    <option value="School Belt" {{ session('report_filters.product') == 'School Belt' ? 'selected' : '' }}>School Belt</option>
                                </select>
                            </div>

                            <!-- Class Filter -->
                            <div class="mb-4">
                                <label for="class" class="form-label">Class</label>
                                <select class="form-select" id="class" name="class">
                                    <option value="">All Classes</option>
                                    <option value="A" {{ session('report_filters.class') == 'A' ? 'selected' : '' }}>Class A</option>
                                    <option value="B" {{ session('report_filters.class') == 'B' ? 'selected' : '' }}>Class B</option>
                                    <option value="C" {{ session('report_filters.class') == 'C' ? 'selected' : '' }}>Class C</option>
                                    <option value="D" {{ session('report_filters.class') == 'D' ? 'selected' : '' }}>Class D</option>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="vs-btn">
                                    <i class="fas fa-chart-line me-2"></i> Generate Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Report Results Section -->
            <div class="col-lg-8">
                @if(session('report_generated') && session('report_data'))
                    @php
                        $reportData = session('report_data');
                    @endphp
                    
                    <!-- Report Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff; border-left: 4px solid #490D59 !important;">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Total Sales</h6>
                                    <h4 class="mb-0" style="color: #490D59;">₹{{ number_format($reportData['summary']['total_sales'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff; border-left: 4px solid #28a745 !important;">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Total Orders</h6>
                                    <h4 class="mb-0" style="color: #28a745;">{{ number_format($reportData['summary']['total_orders'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff; border-left: 4px solid #17a2b8 !important;">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Average Order Value</h6>
                                    <h4 class="mb-0" style="color: #17a2b8;">₹{{ number_format($reportData['summary']['average_order_value'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff; border-left: 4px solid #ffc107 !important;">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Top Product</h6>
                                    <h4 class="mb-0" style="color: #ffc107;">{{ $reportData['summary']['top_product'] ?? 'N/A' }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visual Chart -->
                    <div class="card shadow-sm rounded-4 border-0 mb-4" style="background-color: #ffffff;">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-chart-bar me-2" style="color: #490D59;"></i>Sales Trend
                            </h5>
                            <canvas id="salesChart" height="100"></canvas>
                        </div>
                    </div>

                    <!-- Report Actions -->
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-download me-2" style="color: #490D59;"></i>Download & Share Report
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <button type="button" class="vs-btn w-100" onclick="downloadReport('excel')">
                                        <i class="fas fa-file-excel me-2"></i> Download Excel
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="vs-btn w-100" onclick="downloadReport('pdf')">
                                        <i class="fas fa-file-pdf me-2"></i> Download PDF
                                    </button>
                                </div>
                                <div class="col-12">
                                    <form id="emailReportForm" action="{{ route('frontend.school.email-report') }}" method="POST" class="mt-3">
                                        @csrf
                                        <div class="input-group">
                                            <input type="email" class="form-control" name="email" placeholder="Enter email address" required>
                                            <button type="submit" class="vs-btn" style="background: #490D59;">
                                                <i class="fas fa-envelope me-2"></i> Email Report
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-chart-line fa-5x text-muted mb-4"></i>
                            <h4 class="mb-3">No Report Generated</h4>
                            <p class="text-muted mb-4">Apply filters and click "Generate Report" to view analytics and visual representations.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if(session('report_generated') && session('report_data'))
    @php
        $reportData = session('report_data');
    @endphp
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($reportData['chart_data']['labels'] ?? []) !!},
                datasets: [{
                    label: 'Sales (₹)',
                    data: {!! json_encode($reportData['chart_data']['sales'] ?? []) !!},
                    borderColor: '#490D59',
                    backgroundColor: 'rgba(73, 13, 89, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        function downloadReport(format) {
            window.location.href = '{{ route("frontend.school.download-report") }}?format=' + format;
        }
    </script>
@endif

<style>
    .form-control, .form-select {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 15px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }
</style>
@endsection

