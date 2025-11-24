@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row">
            <!-- Left Sidebar - Account Menu -->
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>

            <!-- Right Content Area -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0" style="background-color: #ffffff; border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0" style="font-weight: 600; color: #333;">My Addresses</h4>
                            <button type="button" class="btn" style="background-color: #28a745; color: #ffffff; border: none; border-radius: 8px; padding: 10px 20px;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i> Add New Address
                            </button>
                        </div>
                        
                        @if(isset($savedAddresses) && count($savedAddresses) > 0)
                            <div class="row g-3">
                                @foreach($savedAddresses as $index => $address)
                                    <div class="col-md-6">
                                        <div class="card border position-relative" style="border-radius: 8px;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><strong>{{ $address['name'] }}</strong></h6>
                                                        <p class="text-muted small mb-1">{{ $address['phone'] }}</p>
                                                        <p class="text-muted small mb-2">{{ $address['email'] }}</p>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" 
                                                                class="btn btn-sm" 
                                                                style="background-color: #28a745; color: #ffffff; border: none; padding: 6px 10px; border-radius: 6px;"
                                                                title="Edit Address"
                                                                onclick="editAddress({{ $index }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('frontend.parent.delete-address', ['addressId' => $index]) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-sm" 
                                                                    style="background-color: #dc3545; color: #ffffff; border: none; padding: 6px 10px; border-radius: 6px;"
                                                                    title="Delete Address">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <p class="mb-2 small">{{ $address['address'] }}</p>
                                                <p class="mb-0 small text-muted">{{ $address['city'] }}, {{ $address['state'] }} - {{ $address['pincode'] }}</p>
                                                @if(!empty($address['landmark']))
                                                    <p class="mb-0 small text-muted">Landmark: {{ $address['landmark'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                                <p class="text-muted mb-4">No addresses saved yet.</p>
                                <button type="button" class="btn" style="background-color: #28a745; color: #ffffff; border: none; border-radius: 8px; padding: 10px 20px;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="fas fa-plus me-2"></i> Add New Address
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add New Address Modal (same as checkout) -->
@include('frontend.checkout.add-address-modal')

<script>
// Store addresses data for editing
const addressesData = @json($savedAddresses ?? []);

function editAddress(index) {
    const address = addressesData[index];
    if (!address) return;
    
    // Set edit mode
    document.getElementById('editingAddressIndex').value = index;
    
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

