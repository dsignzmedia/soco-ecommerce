@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper" style="background-color: #e0e0e0; padding-top: 50px;  border-bottom: 1px solid #d0d0d0;">
    <div class="container" style=" padding: 20px;">
        <div class="breadcumb-menu-wrap" style=" margin: 9px 0 0 0;">
            <ul class="breadcumb-menu">
                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                <li><a href="{{ route('frontend.parent.dashboard') }}">Parent Dashboard</a></li>
                <li>My Orders</li>
            </ul>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff; padding-top: 60px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>
            <div class="col-lg-9">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="h3 mb-0">My Orders</h2>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(count($orders) > 0)
                    @foreach($orders as $order)
                        <div class="card shadow-sm rounded-4 border-0 mb-4" style="background-color: #ffffff;">
                            <div class="card-body p-4">
                                <!-- Order Header -->
                                <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2" style="color: #490D59; font-size: 1.5rem;">Order #SOCO-{{ $order['id'] }}</h5>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-calendar me-1"></i> Placed on {{ date('M d, Y', strtotime($order['created_at'])) }}
                                        </p>
                                        @if(isset($order['student_name']) && $order['student_name'])
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-user-graduate me-1"></i> For: <strong style="color: #490D59;">{{ $order['student_name'] }}</strong>
                                            </p>
                                        @endif
                                        
                                        {{-- Return/Exchange Status Badge --}}
                                        @if(isset($order['return_request']) && $order['return_request'])
                                            @php
                                                $request = $order['return_request'];
                                                $badgeColor = '#6c757d'; // default gray
                                                $badgeText = '';
                                                
                                                if ($request['status'] === 'pending') {
                                                    $badgeColor = '#ffc107'; // yellow
                                                    $badgeText = ucfirst($request['type']) . ' Request Pending';
                                                } elseif ($request['status'] === 'approved') {
                                                    $badgeColor = '#28a745'; // green
                                                    $badgeText = ucfirst($request['type']) . ' Approved';
                                                } elseif ($request['status'] === 'rejected') {
                                                    $badgeColor = '#dc3545'; // red
                                                    $badgeText = ucfirst($request['type']) . ' Rejected';
                                                } elseif ($request['status'] === 'received') {
                                                    $badgeColor = '#17a2b8'; // cyan
                                                    $badgeText = ucfirst($request['type']) . ' Item Received';
                                                } elseif ($request['status'] === 'completed') {
                                                    $badgeColor = '#28a745'; // green
                                                    $badgeText = ucfirst($request['type']) . ' Completed';
                                                }
                                            @endphp
                                            <div class="mt-2">
                                                <span class="badge" style="background-color: {{ $badgeColor }}; color: #ffffff; font-size: 0.85rem; padding: 6px 12px; border-radius: 6px;">
                                                    <i class="fas fa-{{ $request['type'] === 'return' ? 'undo' : 'exchange-alt' }} me-1"></i>
                                                    {{ $badgeText }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0">
                                            <strong style="color: #490D59; font-size: 1.3rem;">₹{{ number_format($order['total']) }}</strong>
                                        </p>
                                    </div>
                                </div>

                                <form action="{{ route('frontend.parent.return-exchange', ['orderId' => $order['id']]) }}" method="GET" id="order-form-{{ $order['id'] }}">
                                    <!-- Order Items - Each product in separate card -->
                                    <div class="mb-4">
                                        @foreach($order['items'] as $item)
                                            <div class="card mb-3 border-0" style="background-color: #f8f5ff; border-radius: 12px;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex gap-3 align-items-start">
                                                        <!-- Selection Checkbox (Hidden by default) -->
                                                        <div class="me-3 item-checkbox-{{ $order['id'] }}" style="display: none;">
                                                            <input type="checkbox" name="selected_items[]" value="{{ $item['id'] }}" id="order_item_{{ $item['id'] }}" style="display: block !important; visibility: visible !important; opacity: 1 !important; width: 24px !important; height: 24px !important; accent-color: #490D59; cursor: pointer;">
                                                            <label for="order_item_{{ $item['id'] }}" class="d-none">Select {{ $item['name'] }}</label>
                                                        </div>

                                                        <div class="flex-shrink-0">
                                                            @if(isset($item['image']) && $item['image'])
                                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e0d5f0;">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border: 1px solid #e0d5f0;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <h6 class="mb-2 pe-2" style="font-size: 1rem; font-weight: 600; color: #333;">{{ $item['name'] ?? 'Unknown Product' }}</h6>
                                                                
                                                                @if(isset($item['return_request']) && $item['return_request'])
                                                                    @php
                                                                        $req = $item['return_request'];
                                                                        $badgeColor = '#6c757d';
                                                                        $statusText = ucfirst($req['type']) . ' ' . ucfirst($req['status']);
                                                                        
                                                                        if ($req['status'] === 'pending') {
                                                                            $badgeColor = '#ffc107'; // yellow
                                                                            $statusText = ucfirst($req['type']) . ' Requested';
                                                                        } elseif ($req['status'] === 'approved') {
                                                                            $badgeColor = '#28a745'; // green
                                                                        } elseif ($req['status'] === 'rejected') {
                                                                            $badgeColor = '#dc3545'; // red
                                                                        } elseif ($req['status'] === 'received') {
                                                                            $badgeColor = '#17a2b8'; // cyan
                                                                        }
                                                                    @endphp
                                                                    <span class="badge ms-2" style="background-color: {{ $badgeColor }}; color: #fff; font-weight: 500; font-size: 0.75rem; white-space: nowrap; position: relative !important;border-radius: 8% !important">
                                                                        {{ $statusText }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            
                                                            <div class="d-flex flex-wrap gap-3 mb-2">
                                                                <p class="text-muted small mb-0">Size: <strong style="color: #490D59;">{{ $item['size'] ?? 'N/A' }}</strong></p>
                                                                <p class="text-muted small mb-0">Quantity: <strong style="color: #490D59;">{{ $item['quantity'] ?? 1 }}</strong></p>
                                                            </div>
                                                            <p class="mb-0">
                                                                <strong style="color: #490D59; font-size: 1.1rem;">₹{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) }}</strong>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 flex-wrap pt-3 border-top">
                                        <a href="{{ route('frontend.parent.track-order', ['orderId' => $order['id']]) }}" class="vs-btn btn-sm" style="background-color: #dc3545; color: #ffffff; border: none; padding: 10px 20px; text-decoration: none;">
                                            <i class="fas fa-truck me-2"></i> Track Order
                                        </a>
                                        
                                        <!-- Toggle Button -->
                                        <button type="button" class="vs-btn btn-sm" onclick="toggleSelection('{{ $order['id'] }}')" id="toggle-btn-{{ $order['id'] }}" style="background: #6c757d; color: #ffffff; border: none; padding: 10px 20px;">
                                            <i class="fas fa-exchange-alt me-2"></i> Return/Exchange
                                        </button>

                                        <!-- Submit Button (Hidden by default) -->
                                        <button type="submit" class="vs-btn btn-sm" id="submit-btn-{{ $order['id'] }}" style="background: #490D59; color: #ffffff; border: none; padding: 10px 20px; display: none;">
                                            <i class="fas fa-check me-2"></i> Proceed with Selected
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-bag fa-5x text-muted mb-3"></i>
                            <h4 class="mb-3">No orders yet</h4>
                            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
                            <a href="{{ route('frontend.parent.dashboard') }}" class="vs-btn">Go to Dashboard</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<script>
    function toggleSelection(orderId) {
        // Toggle checkboxes
        const checkboxes = document.querySelectorAll('.item-checkbox-' + orderId);
        checkboxes.forEach(box => {
            box.style.display = box.style.display === 'none' ? 'block' : 'none';
        });

        // Auto-select if only one item
        const inputs = document.querySelectorAll('.item-checkbox-' + orderId + ' input[type="checkbox"]');
        if (inputs.length === 1) {
            inputs[0].checked = true;
        }

        // Toggle buttons
        const toggleBtn = document.getElementById('toggle-btn-' + orderId);
        const submitBtn = document.getElementById('submit-btn-' + orderId);

        if (submitBtn.style.display === 'none') {
            // Show submit, hide toggle text (or change it)
            submitBtn.style.display = 'inline-block';
            toggleBtn.style.display = 'none';
            // Optional: Add a cancel button logic if needed, but for now simple toggle
        } else {
            submitBtn.style.display = 'none';
            toggleBtn.style.display = 'inline-block';
        }
    }
</script>
@endsection


