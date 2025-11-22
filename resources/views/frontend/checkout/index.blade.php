@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 mb-2">Checkout</h2>
                @if(isset($selectedProfile) && $selectedProfile)
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-graduate me-2"></i>
                        <strong>Shopping for:</strong> {{ $selectedProfile['student_name'] }}
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
                <input type="hidden" name="total" value="{{ $total }}">
                <input type="hidden" name="selected_address" id="selected_address" value="{{ isset($savedAddresses) && count($savedAddresses) > 0 ? '0' : '' }}">
                
                <div class="row">
                    <!-- Shipping Address Section -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 mb-4" style="background-color: #ffffff; border-radius: 12px;">
                            <div class="card-body">
                                <h5 class="mb-4" style="font-weight: 600; color: #333;">Shipping Address</h5>
                                
                                @if(isset($savedAddresses) && count($savedAddresses) > 0)
                                    <!-- Saved Addresses -->
                                    <div class="row g-3 mb-3" id="savedAddressesContainer">
                                        @foreach($savedAddresses as $index => $address)
                                            <div class="col-md-6">
                                                <div class="address-card p-3 border rounded position-relative" 
                                                     style="cursor: pointer; border-color: #e9ecef !important; transition: all 0.3s ease;"
                                                     onclick="selectSavedAddress({{ $index }})"
                                                     id="addressCard_{{ $index }}">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="address_radio" id="address_radio_{{ $index }}" value="{{ $index }}" {{ $index === 0 ? 'checked' : '' }} onchange="setSelectedAddress({{ $index }})">
                                                        <label class="form-check-label w-100" for="address_radio_{{ $index }}" style="cursor: pointer;">
                                                            <div class="d-flex align-items-start gap-2">
                                                                <i class="fas fa-{{ $address['address_type'] === 'home' ? 'home' : ($address['address_type'] === 'office' ? 'building' : 'map-marker-alt') }} mt-1" style="color: #28a745; font-size: 1.2rem;"></i>
                                                                <div class="flex-grow-1">
                                                                    <strong>{{ $address['name'] }}</strong>
                                                                    <p class="mb-1 small text-muted">{{ $address['phone'] }}</p>
                                                                    <p class="mb-0 small text-muted">{{ $address['address'] }}, {{ $address['city'] }}, {{ $address['state'] }} - {{ $address['pincode'] }}</p>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="selected_address" id="selected_address" value="{{ count($savedAddresses) > 0 ? '0' : '' }}">
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
                                
                                <!-- Cart Items -->
                                <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($cartItems as $item)
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
                                </div>
                                
                                <!-- Summary -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #666;">Subtotal:</span>
                                        <span style="color: #333; font-weight: 500;">₹{{ number_format($total) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span style="color: #666;">Shipping:</span>
                                        <span style="color: #28a745; font-weight: 500;">Free</span>
                                    </div>
                                </div>
                                
                                <hr style="margin: 20px 0;">
                                
                                <div class="d-flex justify-content-between mb-4">
                                    <strong style="color: #333; font-size: 1.1rem;">Total:</strong>
                                    <strong style="color: #dc3545; font-size: 1.1rem;">₹{{ number_format($total) }}</strong>
                                </div>
                                
                                <button type="submit" class="vs-btn w-100 mb-2" id="placeOrderBtn" onclick="preventDoubleSubmit(this)">
                                    <i class="fas fa-check-circle me-2"></i> Place Order
                                </button>
                                
                                <a href="{{ route('frontend.parent.cart') }}" class="btn w-100" style="background-color: #6c757d; color: #ffffff; border: none; border-radius: 8px; padding: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#5a6268';" onmouseout="this.style.backgroundColor='#6c757d';">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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

@include('frontend.checkout.add-address-modal')

<script>
// Prevent double form submission
let formSubmitted = false;

// Ensure selected address is set before form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    // Set default address if none selected
    const selectedAddressInput = document.getElementById('selected_address');
    if (!selectedAddressInput.value || selectedAddressInput.value === '') {
        const checkedRadio = document.querySelector('input[name="address_radio"]:checked');
        if (checkedRadio) {
            selectedAddressInput.value = checkedRadio.value;
        }
    }
    
    // Prevent double submission
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
    
    // Allow form to submit normally - don't prevent default
});

function selectSavedAddress(index) {
    document.getElementById('selected_address').value = index;
    document.getElementById('address_radio_' + index).checked = true;
    
    // Update card styling
    document.querySelectorAll('.address-card').forEach(card => {
        card.style.borderColor = '#e9ecef';
        card.style.backgroundColor = '#ffffff';
    });
    if (document.getElementById('addressCard_' + index)) {
        document.getElementById('addressCard_' + index).style.borderColor = '#28a745';
        document.getElementById('addressCard_' + index).style.backgroundColor = '#f8fff9';
    }
}

function setSelectedAddress(index) {
    document.getElementById('selected_address').value = index;
    selectSavedAddress(index);
}

// Style address cards on load
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="address_radio"]:checked');
    if (checkedRadio) {
        const index = checkedRadio.value;
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
