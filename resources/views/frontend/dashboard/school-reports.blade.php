@extends('frontend.layouts.school')

@section('content')

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

        <!-- Horizontal Filter Section -->
        <div class="card shadow-sm rounded-4 border-0 mb-4" style="background-color: #ffffff;">
            <div class="card-body p-4">
                <form id="reportFilterForm" action="{{ route('frontend.school.generate-report') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <!-- Start Date Filter -->
                        <div class="col-md-3">
                             <label for="start_date" class="form-label text-muted small fw-bold text-uppercase">Start Date</label>
                             <input type="date" class="form-control border-0 bg-light rounded-pill px-3" id="start_date" name="start_date" value="{{ session('report_filters.start_date') }}" onclick="this.showPicker()">
                        </div>

                        <!-- End Date Filter -->
                        <div class="col-md-3">
                             <label for="end_date" class="form-label text-muted small fw-bold text-uppercase">End Date</label>
                             <input type="date" class="form-control border-0 bg-light rounded-pill px-3" id="end_date" name="end_date" value="{{ session('report_filters.end_date') }}" onclick="this.showPicker()">
                        </div>

                        <!-- Grade Filter -->
                        <div class="col-md-2">
                            <label for="grade" class="form-label text-muted small fw-bold text-uppercase">Grade</label>
                            <select class="form-select border-0 bg-light rounded-pill px-3" id="grade" name="grade">
                                <option value="">All Grades</option>
                                @if(isset($grades))
                                    @foreach($grades as $g)
                                        <option value="{{ $g }}" {{ session('report_filters.grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Product Filter -->
                        <div class="col-md-2">
                            <label for="product" class="form-label text-muted small fw-bold text-uppercase">Product</label>
                            <select class="form-select border-0 bg-light rounded-pill px-3" id="product" name="product">
                                <option value="">All Products</option>
                                @if(isset($products))
                                    @foreach($products as $p)
                                        <option value="{{ $p }}" {{ session('report_filters.product') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                         <!-- Sale/Status Filter -->
                         <div class="col-md-2">
                            <label for="sale_type" class="form-label text-muted small fw-bold text-uppercase">Status</label>
                            <select class="form-select border-0 bg-light rounded-pill px-3" id="sale_type" name="sale_type">
                                <option value="">All Statuses</option>
                                <option value="completed" {{ session('report_filters.sale_type') == 'completed' ? 'selected' : '' }}>Completed/Paid</option>
                                <option value="pending" {{ session('report_filters.sale_type') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="cancelled" {{ session('report_filters.sale_type') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn rounded-pill px-4 fw-bold" style="background-color: #490D59; color: white;">
                                Apply Filters
                            </button>
                            <a href="{{ route('frontend.school.reports') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold me-2">
                                Reset
                            </a>
                            @if(session('report_generated'))
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="downloadReport('pdf')">
                                    <i class="fas fa-file-pdf me-2"></i> Download PDF
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Full Width Results -->
            <div class="col-12">
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
                            <h5 class="card-title mb-4 d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-chart-bar me-2" style="color: #490D59;"></i>Sales Trend</span>
                                @if(session('report_filters.start_date') || session('report_filters.end_date'))
                                    <small class="text-muted" style="font-size: 0.9rem;">
                                        {{ session('report_filters.start_date') ? \Carbon\Carbon::parse(session('report_filters.start_date'))->format('d M Y') : 'Start' }} 
                                        - 
                                        {{ session('report_filters.end_date') ? \Carbon\Carbon::parse(session('report_filters.end_date'))->format('d M Y') : 'Now' }}
                                    </small>
                                @endif
                            </h5>
                            <canvas id="salesChart" height="100"></canvas>
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
                        display: false,
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

