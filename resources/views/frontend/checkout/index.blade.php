@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 mb-2">Checkout</h2>
                @php
                    // Get unique students from cart
                    $uniqueStudents = collect($cartItems)->pluck('student_name')->unique()->values();
                @endphp
                @if($uniqueStudents->count() > 0)
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-graduate me-2"></i>
                        <strong>Shopping for:</strong> 
                        @if($uniqueStudents->count() == 1)
                            {{ $uniqueStudents->first() }}
                        @else
                            {{ $uniqueStudents->count() }} students ({{ $uniqueStudents->join(', ') }})
                        @endif
                    </p>
                @endif
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('frontend.parent.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('frontend.parent.cart') }}">Cart</a></li>
                        <li class="breadcrumb-item active">Checkout</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(count($cartItems) > 0)
            <form action="{{ route('frontend.parent.process-checkout') }}" method="POST" id="checkoutForm">
                @csrf
                <input type="hidden" name="total" value="{{ $totalWithShipping ?? $total }}">
                <input type="hidden" name="selected_address" id="selected_address" value="{{ isset($savedAddresses) && $savedAddresses->count() > 0 ? '0' : '' }}">
                
                <div class="row">
                    <!-- Shipping Address Section -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 mb-4" style="background-color: #ffffff; border-radius: 12px;">
                            <div class="card-body">
                                <h5 class="mb-4" style="font-weight: 600; color: #333;">Shipping Address</h5>
                                
                                    @if(isset($savedAddresses) && $savedAddresses->count() > 0)
                                        <!-- Saved Addresses -->
                                        <div class="row g-3 mb-3" id="savedAddressesContainer">
                                            @foreach($savedAddresses as $index => $address)
                                                <div class="col-md-6">
                                                    <div class="address-card p-3 border rounded position-relative h-100" 
                                                         style="cursor: pointer; border-color: #e9ecef !important; transition: all 0.3s ease; border-radius: 16px !important;"
                                                         onclick="selectSavedAddress({{ $index }})"
                                                         id="addressCard_{{ $index }}">
                                                        <input type="checkbox" class="address-checkbox d-none" name="address_checkbox_{{ $index }}" id="address_checkbox_{{ $index }}" value="{{ $index }}" {{ $savedAddresses->count() === 1 || $index === 0 ? 'checked' : '' }} onchange="setSelectedAddress({{ $index }})">
                                                        
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <!-- Address Type Badge -->
                                                            <span class="badge" style="background-color: #490D59; color: #fff; padding: 8px 12px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;">
                                                                <i class="fas fa-{{ $address->address_type === 'home' ? 'home' : ($address->address_type === 'office' ? 'building' : 'map-marker-alt') }} me-2"></i>
                                                                {{ ucfirst($address->address_type_display ?? $address->address_type) }}
                                                            </span>

                                                            <!-- Edit/Delete Actions -->
                                                            <div class="d-flex gap-2" onclick="event.stopPropagation();">
                                                                <button type="button" class="btn btn-sm btn-light text-primary" onclick="editAddress({{ $address->id }})" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <form action="{{ route('frontend.parent.delete-address', ['addressId' => $address->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>

                                                        <div class="mb-2">
                                                            <h6 class="mb-2" style="font-weight: 700; color: #333; font-size: 1.1rem;">{{ $address->name }}</h6>
                                                            <p class="mb-2 text-muted" style="font-size: 0.95rem;">
                                                                <i class="fas fa-phone-alt me-2" style="color: #490D59;"></i> {{ $address->phone }}
                                                            </p>
                                                            <p class="mb-2 text-muted" style="font-size: 0.95rem; line-height: 1.5;">
                                                                <i class="fas fa-map-marker-alt me-2" style="color: #490D59;"></i> 
                                                                {{ $address->address }}, {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}
                                                            </p>
                                                            @if(!empty($address->landmark))
                                                                <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                                                    <i class="fas fa-location-arrow me-2" style="color: #490D59;"></i> Landmark: {{ $address->landmark }}
                                                                </p>
                                                            @endif
                                                        </div>

                                                        <!-- Checkmark indicator -->
                                                        <div class="position-absolute bottom-0 end-0 m-3 checkmark-indicator" id="checkmark_{{ $index }}" style="display: {{ $savedAddresses->count() === 1 || $index === 0 ? 'block' : 'none' }};">
                                                            <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.5rem;"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                @else
                                    <!-- No Address Found -->
                                    <div class="text-center py-4 mb-3" id="noAddressContainer">
                                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-3">No addresses found.</p>
                                    </div>
                                @endif
                                
                                <!-- Add Address Button -->
                                <button type="button" class="btn w-100" style="background-color: #28a745; color: #ffffff; border: none; border-radius: 8px; padding: 12px;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="fas fa-plus me-2"></i> Add New Address
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 sticky-top" style="background-color: #ffffff; border-radius: 12px; top: 20px;">
                            <div class="card-body">
                                <h5 class="mb-4" style="font-weight: 600; color: #333;">Order Summary</h5>
                                
                                <!-- Cart Items Grouped by Student -->
                                <div class="mb-3" style="max-height: 400px; overflow-y: auto;">
                                    @php
                                        // Group items by student
                                        $groupedItems = [];
                                        foreach($cartItems as $item) {
                                            $studentName = $item['student_name'] ?? 'Unknown Student';
                                            if (!isset($groupedItems[$studentName])) {
                                                $groupedItems[$studentName] = [];
                                            }
                                            $groupedItems[$studentName][] = $item;
                                        }
                                    @endphp
                                    
                                    @foreach($groupedItems as $studentName => $items)
                                        <!-- Student Header -->
                                        <div class="mb-2 pb-2 border-bottom" style="background-color: #f8f9fa; padding: 8px; border-radius: 6px; margin-bottom: 12px;">
                                            <h6 class="mb-0" style="font-size: 0.875rem; font-weight: 600; color: #490D59;">
                                                <i class="fas fa-user-graduate me-2"></i>{{ $studentName }}
                                            </h6>
                                        </div>
                                        
                                        <!-- Items for this student -->
                                        @foreach($items as $item)
                                            <div class="d-flex gap-2 mb-3 pb-3 border-bottom">
                                                <div class="flex-shrink-0">
                                                    <div style="width: 60px; height: 60px; border-radius: 6px; overflow: hidden; border: 1px solid #e9ecef; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                        @if(isset($item['image']) && $item['image'])
                                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                        @else
                                                            <i class="fas fa-image text-muted"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1" style="font-size: 0.875rem; font-weight: 600;">{{ $item['name'] }}</h6>
                                                    <p class="text-muted small mb-1">Size: {{ $item['size'] }} × Qty: {{ $item['quantity'] }}</p>
                                                    <p class="mb-0" style="color: #dc3545; font-weight: 600; font-size: 0.875rem;">₹{{ number_format($item['item_total']) }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                                
                                <!-- Summary -->
                                <div class="mb-3">
                                    @if(isset($hasInclusiveTax) && $hasInclusiveTax)
                                        <!-- Show inclusive tax message instead of tax breakdown -->
                                        <div class="d-flex justify-content-between mb-2">
                                            <span style="color: #666;">Subtotal:</span>
                                            <span style="color: #333; font-weight: 500;">₹{{ number_format($total) }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small style="color: #28a745; font-style: italic;">
                                                <i class="fas fa-check-circle me-1"></i>Inclusive of all tax
                                            </small>
                                        </div>
                                    @else
                                        <!-- Show tax breakdown -->
                                        <div class="d-flex justify-content-between mb-2">
                                            <span style="color: #666;">Subtotal:</span>
                                            <span style="color: #333; font-weight: 500;">₹{{ number_format($subtotal) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span style="color: #666;">Tax:</span>
                                            <span style="color: #333; font-weight: 500;">₹{{ number_format($totalTax) }}</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #666;">Shipping:</span>
                                        @if(($shippingCost ?? 0) > 0)
                                            <span style="color: #333; font-weight: 500;">₹{{ number_format($shippingCost, 2) }}</span>
                                        @else
                                            <span style="color: #28a745; font-weight: 500;">Free</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <hr style="margin: 20px 0;">
                                
    <div class="d-flex justify-content-between mb-4">
                                    <strong style="color: #333; font-size: 1.1rem;">Total:</strong>
                                    <strong style="color: #dc3545; font-size: 1.1rem;">₹{{ number_format($totalWithShipping ?? $total) }}</strong>
                                </div>
                                
                                @if(isset($razorpayEnabled) && $razorpayEnabled)
    <button type="button" class="vs-btn w-100 mb-2" id="payWithRazorpayBtn" onclick="initiatePayment()">
        <i class="fas fa-credit-card me-2"></i> Pay with Razorpay
    </button>
@endif                                
                                <button type="submit" class="vs-btn w-100 mb-2" id="placeOrderBtn" onclick="preventDoubleSubmit(this)" style="background-color: #2C3E50; color: #ffffff; border-color: #2C3E50;">
                                    <i class="fas fa-money-bill-wave me-2"></i> Pay Now 
                                </button>
                                
                                <a href="{{ route('frontend.parent.cart') }}" class="btn w-100" style="background-color: #6c757d; color: #ffffff; border: none; border-radius: 8px; padding: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#5a6268';" onmouseout="this.style.backgroundColor='#6c757d';">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Razorpay Script -->
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script src="{{ asset('assets/js/razorpay-checkout.js') }}"></script>
            <script>
                function initiatePayment() {
                    initiateRazorpayPayment({
                        initiateRoute: "{{ route('frontend.parent.payment.initiate') }}",
                        verifyRoute: "{{ route('frontend.parent.payment.verify') }}",
                        csrfToken: "{{ csrf_token() }}",
                        totalAmount: {{ $totalWithShipping ?? $total }}
                    });
                }

                // Auto-detect Test Mode and show helper
                document.addEventListener('DOMContentLoaded', function() {
                    // We don't have the key directly here, but we can infer or waiting for the initiate response.
                    // For better UX, let's just make the modal available.
                });
            </script>
        @else
            <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                    <h4 class="mb-3">Your cart is empty</h4>
                    <p class="text-muted mb-4">Add items to your cart before checkout.</p>
                    <a href="{{ route('frontend.parent.cart') }}" class="vs-btn">Go to Cart</a>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Test Mode Credentials Modal -->
<div class="modal fade" id="testCredentialsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark"><i class="fas fa-exclamation-triangle me-2"></i>Test Mode Active</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You are using a <strong>Test Key</strong>. Real payments will fail.</p>
                <p>Please use the following <strong>Test Card Details</strong>:</p>
                <div class="p-3 bg-light rounded border">
                    <p class="mb-1"><strong>Card Number:</strong> <span class="user-select-all font-monospace select-all">4111 1111 1111 1111</span></p>
                    <p class="mb-1"><strong>Expiry:</strong> Any future date (e.g. 12/35)</p>
                    <p class="mb-0"><strong>CVV:</strong> 123</p>
                </div>
                <p class="mt-3 text-muted small"><strong>Note:</strong> Do not use your actual credit/debit card.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we are in test mode (this variable should be passed from backend or inferred)
        // Since we can't easily pass it from controller without modifying it, we'll check the key prefix loosely if possible,
        // but for now, let's just show it if the user clicks a "Show Test Details" link which we'll add via JS if the key starts with 'rzp_test'
    });
</script>

@include('frontend.checkout.add-address-modal')

<script>
// Store addresses data for editing
const addressesData = @json($savedAddresses ?? []);

function editAddress(addressId) {
    const address = addressesData.find(addr => addr.id === addressId);
    if (!address) return;
    
    // Set edit mode
    document.getElementById('editingAddressIndex').value = addressId;
    
    // Update modal title and button
    document.getElementById('addAddressModalLabel').textContent = 'Edit Address';
    document.getElementById('saveAddressBtn').textContent = 'Update Address';
    
    // Pre-fill form fields
    document.getElementById('modal_name').value = address.name || '';
    document.getElementById('modal_phone').value = address.phone || '';
    document.getElementById('modal_email').value = address.email || '';
    document.getElementById('modal_alternative_number').value = address.alternative_number || '';
    document.getElementById('modal_block_name').value = address.block_name || '';
    document.getElementById('modal_address').value = address.address || '';
    document.getElementById('modal_city').value = address.city || '';
    document.getElementById('modal_state').value = address.state || '';
    document.getElementById('modal_pincode').value = address.pincode || '';
    document.getElementById('modal_landmark').value = address.landmark || '';
    
    // Set address type
    const addressType = address.address_type || 'home';
    selectModalAddressType(addressType);
    
    // Handle custom address type
    if (addressType === 'others' && address.address_type_display) {
        document.getElementById('customAddressTypeContainer').style.display = 'block';
        document.getElementById('modal_custom_address_type').value = address.address_type_display;
        document.getElementById('modal_custom_address_type').required = true;
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
    modal.show();
}

// Prevent double form submission
let formSubmitted = false;

// Ensure selected address is set before form submission
const checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(e) {
        const selectedAddressInput = document.getElementById('selected_address');
        if (selectedAddressInput && (!selectedAddressInput.value || selectedAddressInput.value === '')) {
            const checkedRadio = document.querySelector('input[name="address_radio"]:checked');
            if (checkedRadio) {
                selectedAddressInput.value = checkedRadio.value;
            }
        }
        
        if (formSubmitted) {
            e.preventDefault();
            return false;
        }
        formSubmitted = true;
        
        const submitBtn = document.getElementById('placeOrderBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
        }
    });
}

function selectSavedAddress(index) {
    const selectedAddressInput = document.getElementById('selected_address');
    if (selectedAddressInput) {
        selectedAddressInput.value = index;
    }
    
    const checkbox = document.getElementById('address_checkbox_' + index);
    if (checkbox) {
        checkbox.checked = true;
    }
    
    // Update card styling and checkmarks
    document.querySelectorAll('.address-card').forEach((card, i) => {
        card.style.borderColor = '#e9ecef';
        card.style.backgroundColor = '#ffffff';
        const checkmark = document.getElementById('checkmark_' + i);
        const cb = document.getElementById('address_checkbox_' + i);
        if (checkmark && cb) {
            checkmark.style.display = 'none';
            cb.checked = false;
        }
    });
    
    if (document.getElementById('addressCard_' + index)) {
        document.getElementById('addressCard_' + index).style.borderColor = '#28a745';
        document.getElementById('addressCard_' + index).style.backgroundColor = '#f8fff9';
        const checkmark = document.getElementById('checkmark_' + index);
        if (checkmark && checkbox) {
            checkmark.style.display = 'block';
            checkbox.checked = true;
        }
    }
}

function setSelectedAddress(index) {
    const selectedAddressInput = document.getElementById('selected_address');
    if (selectedAddressInput) {
        selectedAddressInput.value = index;
    }
    selectSavedAddress(index);
}

// Style address cards on load
document.addEventListener('DOMContentLoaded', function() {
    const checkedCheckbox = document.querySelector('input[class*="address-checkbox"]:checked');
    if (checkedCheckbox) {
        const index = checkedCheckbox.value;
        selectSavedAddress(index);
    }
});
</script>

<style>
.address-card:hover {
    border-color: #28a745 !important;
    background-color: #f8fff9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.1);
}

/* Modal z-index fixes */
.modal {
    z-index: 10500 !important;
}

.modal-backdrop {
    z-index: 10400 !important;
    background-color: rgba(0, 0, 0, 0.7) !important;
}

.modal-dialog {
    z-index: 10510 !important;
}

.modal-content {
    z-index: 10520 !important;
}

/* Ensure header doesn't overlap */
.vs-header {
    z-index: 1000 !important;
}
</style>
@endsection
