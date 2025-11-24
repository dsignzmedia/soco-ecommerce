<!-- Add New Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="z-index: 9999;">
        <div class="modal-content" style="border-radius: 12px; z-index: 10000; position: relative;">
            <div class="modal-header border-0 pb-0" style="position: relative;">
                <h5 class="modal-title w-100 text-center" id="addAddressModalLabel" style="font-weight: 600; font-size: 1.25rem;">Add New Address</h5>
                <input type="hidden" id="editingAddressIndex" value="">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; background-color: #dc3545; opacity: 1; border-radius: 50%; width: 30px; height: 30px; padding: 0;">
                    <span aria-hidden="true" style="color: #ffffff; font-size: 1.2rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newAddressForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="modal_phone" name="phone" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="modal_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_alternative_number" class="form-label">Alternative Number</label>
                            <input type="tel" class="form-control" id="modal_alternative_number" name="alternative_number">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_block_name" class="form-label">Block name</label>
                            <input type="text" class="form-control" id="modal_block_name" name="block_name">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_state" class="form-label">State <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_state" name="state" required>
                                <option value="">Choose State</option>
                                <option value="Tamil Nadu">Tamil Nadu</option>
                                <option value="Kerala">Kerala</option>
                                <option value="Karnataka">Karnataka</option>
                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                <option value="Telangana">Telangana</option>
                                <option value="Maharashtra">Maharashtra</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Gujarat">Gujarat</option>
                                <option value="Rajasthan">Rajasthan</option>
                                <option value="West Bengal">West Bengal</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="modal_address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="modal_address" name="address" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_city" class="form-label">Town/City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_city" name="city" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_pincode" class="form-label">Pin Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_pincode" name="pincode" required>
                        </div>
                        <div class="col-12">
                            <label for="modal_landmark" class="form-label">Landmark</label>
                            <input type="text" class="form-control" id="modal_landmark" name="landmark">
                        </div>
                        
                        <!-- Address Type Selection -->
                        <div class="col-12">
                            <label class="form-label mb-3">Address Type</label>
                            <div class="d-flex gap-3">
                                <button type="button" class="btn address-type-btn active" data-type="home" onclick="selectModalAddressType('home')" style="flex: 1; background-color: #28a745; color: #ffffff; border: 2px solid #28a745; border-radius: 8px; padding: 12px;">
                                    <i class="fas fa-home me-2"></i> Home
                                </button>
                                <button type="button" class="btn address-type-btn" data-type="office" onclick="selectModalAddressType('office')" style="flex: 1; background-color: #ffffff; color: #28a745; border: 2px solid #28a745; border-radius: 8px; padding: 12px;">
                                    <i class="fas fa-building me-2"></i> Office
                                </button>
                                <button type="button" class="btn address-type-btn" data-type="others" onclick="selectModalAddressType('others')" style="flex: 1; background-color: #ffffff; color: #28a745; border: 2px solid #28a745; border-radius: 8px; padding: 12px;">
                                    <i class="fas fa-map-marker-alt me-2"></i> Others
                                </button>
                            </div>
                            <input type="hidden" name="address_type" id="modal_address_type" value="home">
                            
                            <!-- Custom Address Type Input (shown only when "others" is selected) -->
                            <div id="customAddressTypeContainer" style="display: none; margin-top: 15px;">
                                <label for="modal_custom_address_type" class="form-label">Enter Address Type Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_custom_address_type" name="custom_address_type" placeholder="e.g., Work, Vacation Home, etc.">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn" style="background-color: #000000; color: #ffffff; border: none; border-radius: 8px; padding: 10px 30px;" data-bs-dismiss="modal" onclick="resetAddressModal()">Cancel</button>
                <button type="button" class="btn" style="background-color: #28a745; color: #ffffff; border: none; border-radius: 8px; padding: 10px 30px;" onclick="saveNewAddress()" id="saveAddressBtn">Add Address</button>
            </div>
        </div>
    </div>
</div>

<script>
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
    let url;
    if (isEditing) {
        // Build the update URL with the address index
        url = '/parent/update-address/' + editingIndex;
    } else {
        url = '{{ route("frontend.parent.save-address") }}';
    }
    
    // Submit via AJAX to save/update address
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
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
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addAddressModal'));
            modal.hide();
            
            // Reload page to show updated address
            location.reload();
        } else {
            alert(data.message || 'Error saving address. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving address. Please try again.');
    });
}
</script>

<style>
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
</style>

<script>
// Reset modal when opened for adding (not editing)
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addAddressModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            // Only reset if not editing
            const editingIndex = document.getElementById('editingAddressIndex');
            if (editingIndex && !editingIndex.value) {
                resetAddressModal();
            }
        });
        
        modal.addEventListener('hidden.bs.modal', function() {
            resetAddressModal();
        });
    }
});
</script>

