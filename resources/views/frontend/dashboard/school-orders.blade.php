@extends('frontend.layouts.school')

@section('content')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="h3 mb-2">Orders Management</h2>
                        <p class="text-muted mb-0">{{ $school->name }}</p>
                    </div>
                    <a href="{{ route('frontend.school.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Analytics Section -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #490D59 !important;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Orders</h6>
                        <h3 class="mb-0" style="color: #490D59;">{{ number_format($analytics['total_orders']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #28a745 !important;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h3 class="mb-0" style="color: #28a745;">₹{{ number_format($analytics['total_revenue']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #ffc107 !important;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Pending</h6>
                        <h3 class="mb-0" style="color: #ffc107;">{{ number_format($analytics['pending_orders']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm rounded-4 border-0 h-100" style="background-color: #ffffff; border-left: 4px solid #17a2b8 !important;">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Completed</h6>
                        <h3 class="mb-0" style="color: #17a2b8;">{{ number_format($analytics['completed_orders']) }}</h3>
                    </div>
                </div>
            </div>
        </div>



        <!-- Filters -->
        <div class="card shadow-sm rounded-4 border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('frontend.school.orders') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Order #, Student, Customer" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('frontend.school.orders') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f5ff;">
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Grade</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->order_date->format('d M Y') }}</td>
                                    <td>{{ $order->student_name }}</td>
                                    <td>{{ $order->grade }}</td>
                                    <td>{{ $order->item_name }} ({{ $order->quantity }})</td>
                                    <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'completed' => 'success',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                            ];
                                            $color = $statusColors[$order->order_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($order->order_status) }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>

                                <!-- Order Details Modal -->
                                <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #490D59; color: white;">
                                                <h5 class="modal-title">Order Details - {{ $order->order_number }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="text-muted mb-3">Student Information</h6>
                                                        <p><strong>Name:</strong> {{ $order->student_name }}</p>
                                                        <p><strong>Grade:</strong> {{ $order->grade }}</p>
                                                        <p><strong>Category:</strong> {{ $order->category }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="text-muted mb-3">Customer Information</h6>
                                                        <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                                                        <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                                                        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h6 class="text-muted mb-3">Order Details</h6>
                                                        <p><strong>Item:</strong> {{ $order->item_name }}</p>
                                                        <p><strong>Size:</strong> {{ $order->size }}</p>
                                                        <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                                                        <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}</p>
                                                        <p><strong>Payment Status:</strong> <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($order->payment_status) }}</span></p>
                                                        <p><strong>Order Status:</strong> <span class="badge bg-{{ $statusColors[$order->order_status] ?? 'secondary' }}">{{ ucfirst($order->order_status) }}</span></p>
                                                    </div>
                                                </div>
                                                @if($order->tracking_number)
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h6 class="text-muted mb-3">Shipping Information</h6>
                                                            <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
                                                            <p><strong>Courier:</strong> {{ $order->courier_name }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No orders found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .btn-primary {
        background-color: #490D59;
        border-color: #490D59;
    }
    .btn-primary:hover {
        background-color: #3a0a47;
        border-color: #3a0a47;
    }
</style>
@endsection
