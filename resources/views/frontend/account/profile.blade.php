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
                    <div class="card-header" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); padding: 24px; border: none;">
                        <h4 class="mb-0" style="font-weight: 600; color: #ffffff; font-size: 1.5rem;">
                            <i class="fas fa-user-circle me-2"></i> Personal Information
                        </h4>
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

                        <form action="{{ route('frontend.parent.update-profile-details') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        <i class="fas fa-user me-2 text-muted"></i>Full Name
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                            value="{{ old('name', $user->name) }}" required
                                            style="border-radius: 10px 0 0 10px; padding: 12px 16px; border: 2px solid #e0e0e0; transition: all 0.3s;"
                                            onfocus="this.style.borderColor='#490D59'"
                                            onblur="this.style.borderColor='#e0e0e0'"
                                            {{ $user->name ? 'readonly' : '' }}>
                                        <button type="button" class="btn" id="editNameBtn"
                                            style="background-color: #490D59; color: #ffffff; border: none; border-radius: 0 10px 10px 0; padding: 12px 16px;"
                                            onclick="toggleNameEdit()">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        <i class="fas fa-envelope me-2 text-muted"></i>Email Address
                                    </label>
                                    <div class="input-group">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                            value="{{ $user->email }}" 
                                            placeholder="{{ !$user->email ? 'Enter your email address' : '' }}"
                                            style="border-radius: 10px 0 0 10px; padding: 12px 16px; border: 2px solid #e0e0e0; transition: all 0.3s;"
                                            onfocus="this.style.borderColor='#490D59'"
                                            onblur="this.style.borderColor='#e0e0e0'"
                                            readonly>
                                        
                                        <button type="button" class="btn" id="editEmailBtn"
                                            style="background-color: {{ $user->email ? '#490D59' : '#28a745' }}; color: #ffffff; border: none; border-radius: 0 10px 10px 0; padding: 12px 16px;"
                                            onclick="toggleEmailEdit()">
                                            <i class="fas {{ $user->email ? 'fa-edit' : 'fa-plus' }}"></i>
                                        </button>
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    @if(!$user->email)
                                        <div class="form-text text-warning" style="margin-top: 6px; font-size: 0.875rem;">
                                            <i class="fas fa-exclamation-circle me-1"></i>Please update your email address.
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        <i class="fas fa-phone me-2 text-muted"></i>Phone Number
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" 
                                            value="{{ old('phone', $user->phone) }}" placeholder="Enter phone number"
                                            style="border-radius: 10px 0 0 10px; padding: 12px 16px; border: 2px solid #e0e0e0; transition: all 0.3s;"
                                            onfocus="this.style.borderColor='#490D59'"
                                            onblur="this.style.borderColor='#e0e0e0'"
                                            {{ $user->phone ? 'readonly' : '' }}>
                                        <button type="button" class="btn" id="editPhoneBtn"
                                            style="background-color: {{ $user->phone ? '#490D59' : '#28a745' }}; color: #ffffff; border: none; border-radius: 0 10px 10px 0; padding: 12px 16px;"
                                            onclick="togglePhoneEdit()">
                                            <i class="fas {{ $user->phone ? 'fa-edit' : 'fa-plus' }}"></i>
                                        </button>
                                        @error('phone')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    @if($user->phone)
                                        <div class="form-text" style="margin-top: 6px; font-size: 0.875rem;">
                                            <i class="fas fa-info-circle me-1"></i>You can also use this mobile number to log in if you lost your email address.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 pt-3" style="border-top: 1px solid #f0f0f0;">
                                <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #490D59 0%, #6B1B7F 100%); color: #ffffff; border: none; border-radius: 12px; padding: 14px 36px; font-weight: 600; box-shadow: 0 4px 12px rgba(73, 13, 89, 0.3); transition: all 0.3s;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(73, 13, 89, 0.4)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(73, 13, 89, 0.3)'">
                                    <i class="fas fa-save me-2"></i> Update Profile
                                </button>
                            </div>
                        </form>
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
                                                <div class="d-flex justify-content-between align-items-start mb-3">
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
                                                    <div class="d-flex gap-2">
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

<script>
// Toggle phone number edit
function togglePhoneEdit() {
    const phoneInput = document.getElementById('phone');
    const editBtn = document.getElementById('editPhoneBtn');
    
    if (phoneInput.hasAttribute('readonly')) {
        phoneInput.removeAttribute('readonly');
        phoneInput.focus();
        editBtn.innerHTML = '<i class="fas fa-check"></i>';
        editBtn.style.backgroundColor = '#28a745';
    } else {
        phoneInput.setAttribute('readonly', 'readonly');
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.style.backgroundColor = '#490D59';
    }
}

// Toggle name edit
function toggleNameEdit() {
    const nameInput = document.getElementById('name');
    const editBtn = document.getElementById('editNameBtn');
    
    if (nameInput.hasAttribute('readonly')) {
        nameInput.removeAttribute('readonly');
        nameInput.focus();
        editBtn.innerHTML = '<i class="fas fa-check"></i>';
        editBtn.style.backgroundColor = '#28a745';
    } else {
        nameInput.setAttribute('readonly', 'readonly');
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.style.backgroundColor = '#490D59';
    }
}

// Toggle email edit
function toggleEmailEdit() {
    const emailInput = document.getElementById('email');
    const editBtn = document.getElementById('editEmailBtn');
    
    if (emailInput.hasAttribute('readonly')) {
        emailInput.removeAttribute('readonly');
        emailInput.focus();
        editBtn.innerHTML = '<i class="fas fa-check"></i>';
        editBtn.style.backgroundColor = '#28a745';
    } else {
        emailInput.setAttribute('readonly', 'readonly');
        editBtn.innerHTML = '<i class="fas fa-edit"></i>';
        editBtn.style.backgroundColor = '#490D59';
    }
}
</script>

<!-- Include Address Modal -->
@include('frontend.checkout.add-address-modal')

<script>
// Store addresses data for editing
const addressesData = @json($savedAddresses);

let selectedAddressType = 'home';

function selectModalAddressType(type) {
    selectedAddressType = type;
    document.getElementById('modal_address_type').value = type;
    document.querySelectorAll('#addAddressModal .address-type-btn').forEach(btn => {
        if (btn.dataset.type === type) {
            btn.style.backgroundColor = '#28a745';
            btn.style.color = '#ffffff';
            btn.style.borderColor = '#28a745';
        } else {
            btn.style.backgroundColor = '#ffffff';
            btn.style.color = '#28a745';
            btn.style.borderColor = '#28a745';
        }
    });
    
    // Show/hide custom address type input
    const customContainer = document.getElementById('customAddressTypeContainer');
    const customInput = document.getElementById('modal_custom_address_type');
    if (type === 'others') {
        customContainer.style.display = 'block';
        customInput.required = true;
    } else {
        customContainer.style.display = 'none';
        customInput.required = false;
        customInput.value = '';
    }
}

function resetAddressModal() {
    // Reset form
    document.getElementById('newAddressForm').reset();
    
    // Reset address type to home
    selectedAddressType = 'home';
    document.getElementById('modal_address_type').value = 'home';
    document.getElementById('editingAddressIndex').value = '';
    
    // Reset modal title and button
    document.getElementById('addAddressModalLabel').textContent = 'Add New Address';
    document.getElementById('saveAddressBtn').textContent = 'Add Address';
    
    // Reset button styles
    document.querySelectorAll('#addAddressModal .address-type-btn').forEach(btn => {
        if (btn.dataset.type === 'home') {
            btn.style.backgroundColor = '#28a745';
            btn.style.color = '#ffffff';
            btn.style.borderColor = '#28a745';
        } else {
            btn.style.backgroundColor = '#ffffff';
            btn.style.color = '#28a745';
            btn.style.borderColor = '#28a745';
        }
    });
    
    // Hide custom address type input
    document.getElementById('customAddressTypeContainer').style.display = 'none';
    document.getElementById('modal_custom_address_type').required = false;
    document.getElementById('modal_custom_address_type').value = '';
}

function saveNewAddress() {
    const form = document.getElementById('newAddressForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validate custom address type if "others" is selected
    const customAddressType = selectedAddressType === 'others' ? document.getElementById('modal_custom_address_type').value.trim() : null;
    if (selectedAddressType === 'others' && !customAddressType) {
        alert('Please enter a custom address type name.');
        document.getElementById('modal_custom_address_type').focus();
        return;
    }
    
    // Check if editing or adding
    const editingIndex = document.getElementById('editingAddressIndex').value;
    const isEditing = editingIndex !== '';
    
    // Determine the URL
    let url = '{{ route("frontend.parent.save-address") }}';
    
    // Submit via AJAX to save/update address
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            name: document.getElementById('modal_name').value,
            phone: document.getElementById('modal_phone').value,
            email: document.getElementById('modal_email').value,
            alternative_number: document.getElementById('modal_alternative_number').value,
            block_name: document.getElementById('modal_block_name').value,
            address: document.getElementById('modal_address').value,
            city: document.getElementById('modal_city').value,
            state: document.getElementById('modal_state').value,
            pincode: document.getElementById('modal_pincode').value,
            landmark: document.getElementById('modal_landmark').value,
            address_type: selectedAddressType,
            custom_address_type: customAddressType,
            editing_address_index: editingIndex || null,
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addAddressModal'));
            modal.hide();
            
            // Reload page to show updated address
            location.reload();
        } else {
            console.error('Save failed:', data.message);
            alert(data.message || 'Error saving address. Please try again.');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error saving address. Please try again.');
    });
}

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
