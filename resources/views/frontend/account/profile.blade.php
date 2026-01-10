@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper" style="background-color: #e0e0e0; padding-top: 50px; border-bottom: 1px solid #d0d0d0;">
    <div class="container" style="padding: 20px;">
        <div class="breadcumb-menu-wrap" style="margin: 9px 0 0 0;">
            <ul class="breadcumb-menu">
                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                <li><a href="{{ route('frontend.parent.dashboard') }}">Parent Dashboard</a></li>
                <li>Profile</li>
            </ul>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff; padding: 60px 0;">
    <div class="container">
        <div class="row">
            <!-- Left Sidebar - Account Menu -->
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>

            <!-- Right Content Area -->
            <div class="col-lg-9">
                <!-- Personal Information Card -->
                <div class="card shadow-sm border-0 mb-4" style="background-color: #ffffff; border-radius: 16px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); padding: 20px 24px; border: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0" style="font-weight: 600; color: #ffffff; font-size: 1.25rem;">
                                <i class="fas fa-user-circle me-2"></i> Personal Information
                            </h4>
                            <button type="button" class="btn btn-light btn-sm" 
                                style="color: #490D59; font-weight: 600; border-radius: 8px; padding: 8px 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);" 
                                data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="fas fa-edit me-2"></i> Edit Profile
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #28a745;">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #dc3545;">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                    <div class="d-flex flex-column">
                                        <!-- Full Name -->
                                        <div class="d-flex align-items-center py-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; background-color: #eaddf5; color: #490D59;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <label class="form-label text-muted mb-0 fw-bold small text-uppercase">Full Name</label>
                                                <h5 class="mb-0 text-dark fw-bold" style="font-size: 14px;">{{ $user->name ?: 'Not Provided' }}</h5>
                                            </div>
                                        </div>
                                        
                                        <hr class="my-0" style="color: #e0e0e0;">

                                        <!-- Email Address -->
                                        <div class="d-flex align-items-center py-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; background-color: #eaddf5; color: #490D59;">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <label class="form-label text-muted mb-0 fw-bold small text-uppercase">Email Address</label>
                                                <h5 class="mb-0 text-dark fw-bold" style="font-size: 14px;">{{ $user->email ?: 'Not Provided' }}</h5>
                                                @if(!$user->email)
                                                    <div class="text-warning mt-1 small">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Please update your email address.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <hr class="my-0" style="color: #e0e0e0;">

                                        <!-- Phone Number -->
                                        <div class="d-flex align-items-center py-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px; background-color: #eaddf5; color: #490D59;">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <label class="form-label text-muted mb-0 fw-bold small text-uppercase">Phone Number</label>
                                                <h5 class="mb-0 text-dark fw-bold" style="font-size: 14px;">{{ $user->phone ?: 'Not Provided' }}</h5>
                                                @if($user->phone)
                                                    <div class="text-muted mt-1 small" style="font-size: 0.75rem;">
                                                        <i class="fas fa-info-circle me-1"></i>Used for login recovery.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Addresses Card -->
                <div class="card shadow-sm border-0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); padding: 24px; border: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0" style="font-weight: 600; color: #ffffff; font-size: 1.5rem;">
                                <i class="fas fa-map-marker-alt me-2"></i> My Addresses
                            </h4>
                            <button type="button" class="btn" style="background-color: #ffffff; color: #490D59; border: none; border-radius: 10px; padding: 10px 24px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i> Add Address
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $savedAddresses = $user->addresses;
                        @endphp

                        @if($savedAddresses->count() > 0)
                            <div class="row g-4">
                                @foreach($savedAddresses as $address)
                                    <div class="col-md-6">
                                        <div class="card h-100" style="border-radius: 12px; border: 2px solid #f0f0f0; transition: all 0.3s; overflow: hidden;"
                                            onmouseover="this.style.borderColor='#490D59'; this.style.boxShadow='0 4px 12px rgba(73, 13, 89, 0.15)'"
                                            onmouseout="this.style.borderColor='#f0f0f0'; this.style.boxShadow='none'">
                                            <div class="card-body p-3">
                                                <!-- Top Row: Badge and Buttons -->
                                                <div class="d-flex justify-content-between align-items-start mb-3 w-100">
                                                    <!-- Left: Badge -->
                                                    <span class="badge" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); font-size: 0.8rem; padding: 6px 12px; border-radius: 8px;">
                                                        @if($address->address_type === 'home')
                                                            <i class="fas fa-home me-1"></i>
                                                        @elseif($address->address_type === 'office')
                                                            <i class="fas fa-building me-1"></i>
                                                        @else
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                        @endif
                                                        {{ $address->address_type_display }}
                                                    </span>
                                                    
                                                    <!-- Right: Action Buttons -->
                                                    <div class="d-flex gap-2 ms-auto">
                                                        <button type="button" 
                                                                class="btn btn-sm" 
                                                                style="background-color: #490D59; color: #ffffff; border: none; padding: 8px 12px; border-radius: 8px; transition: all 0.3s;"
                                                                title="Edit Address"
                                                                onclick="editAddress({{ $address->id }})"
                                                                onmouseover="this.style.backgroundColor='#6B1B7F'"
                                                                onmouseout="this.style.backgroundColor='#490D59'">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('frontend.parent.delete-address', ['addressId' => $address->id]) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-sm" 
                                                                    style="background-color: #dc3545; color: #ffffff; border: none; padding: 8px 12px; border-radius: 8px; transition: all 0.3s;"
                                                                    title="Delete Address"
                                                                    onmouseover="this.style.backgroundColor='#c82333'"
                                                                    onmouseout="this.style.backgroundColor='#dc3545'">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                
                                                <!-- Address Details -->
                                                <h6 class="mb-2" style="font-weight: 600; color: #333; font-size: 1.1rem;">{{ $address->name }}</h6>
                                                <div class="mb-2" style="color: #666; font-size: 0.9rem;">
                                                    <i class="fas fa-phone me-2" style="color: #490D59; width: 16px;"></i>{{ $address->phone }}
                                                </div>

                                                <div class="mb-2" style="color: #555; font-size: 0.95rem; line-height: 1.6;">
                                                    <i class="fas fa-map-marker-alt me-2" style="color: #490D59; width: 16px;"></i>{{ $address->address }}
                                                </div>
                                                <div style="color: #777; font-size: 0.9rem;">
                                                    <i class="fas fa-location-dot me-2" style="color: #490D59; width: 16px;"></i>{{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}
                                                </div>
                                                @if(!empty($address->landmark))
                                                    <div class="mt-2" style="color: #777; font-size: 0.85rem;">
                                                        <i class="fas fa-location-arrow me-2" style="color: #490D59; width: 16px;"></i>Landmark: {{ $address->landmark }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5" style="background-color: #f8f9fa; border-radius: 12px;">
                                <div style="font-size: 4rem; color: #e0e0e0; margin-bottom: 20px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <h5 style="color: #666; margin-bottom: 16px;">No addresses saved yet</h5>
                                <p class="text-muted mb-4">Add your first address to get started</p>
                                <button type="button" class="btn btn-lg" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); color: #ffffff; border: none; border-radius: 12px; padding: 14px 32px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="fas fa-plus me-2"></i> Add Your First Address
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); padding: 20px 24px; border: none;">
                <h5 class="modal-title text-white fw-bold" id="editProfileModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Edit Personal Information
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('frontend.parent.update-profile-details') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="modal_profile_name" class="form-label fw-bold small text-uppercase text-muted">Full Name</label>
                        <input type="text" class="form-control" id="modal_profile_name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Enter your full name">
                    </div>
                    <div class="mb-3">
                        <label for="modal_profile_email" class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                        <input type="email" class="form-control" id="modal_profile_email" name="email" value="{{ $user->email }}" placeholder="Enter your email address">
                        <div class="form-text small">We'll use this for order updates.</div>
                    </div>
                    <div class="mb-3">
                        <label for="modal_profile_phone" class="form-label fw-bold small text-uppercase text-muted">Phone Number</label>
                        <input type="text" class="form-control" id="modal_profile_phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Enter your phone number">
                        <div class="form-text small">Used for account recovery.</div>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light border-0">
                    <button type="button" class="btn btn-light text-muted fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background-color: #490D59; color: white; font-weight: 600;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Address Modal -->
@include('frontend.checkout.add-address-modal')

<script>
// Store addresses data for editing
const addressesData = @json($savedAddresses);


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
    selectedAddressType = addressType;
    document.getElementById('modal_address_type').value = addressType;
    
    // Update button styles
    document.querySelectorAll('#addAddressModal .address-type-btn').forEach(btn => {
        if (btn.dataset.type === addressType) {
            btn.style.backgroundColor = '#28a745';
            btn.style.color = '#ffffff';
            btn.style.borderColor = '#28a745';
        } else {
            btn.style.backgroundColor = '#ffffff';
            btn.style.color = '#28a745';
            btn.style.borderColor = '#28a745';
        }
    });
    
    // Handle custom address type
    if (addressType === 'others' && address.address_type_display) {
        document.getElementById('customAddressTypeContainer').style.display = 'block';
        document.getElementById('modal_custom_address_type').value = address.address_type_display;
        document.getElementById('modal_custom_address_type').required = true;
    } else {
        document.getElementById('customAddressTypeContainer').style.display = 'none';
        document.getElementById('modal_custom_address_type').value = '';
        document.getElementById('modal_custom_address_type').required = false;
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addAddressModal'));
    modal.show();
}
</script>
@endsection
