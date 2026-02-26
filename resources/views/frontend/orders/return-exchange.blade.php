    @extends('frontend.layouts.app')

    @section('content')
    @include('frontend.partials.header')

    <section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h3 mb-2">Exchange</h2>
                            <p class="text-muted mb-0">Order #{{ $order['id'] }}</p>
                        </div>
                        <a href="{{ route('frontend.parent.orders') }}" class="vs-btn btn-sm d-none d-md-inline-flex">
                            <i class="fas fa-arrow-left me-2"></i> Back to Orders
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('frontend.parent.request-return-exchange') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                            <div class="card-body p-4">
                                <h5 class="mb-4">Request Exchange</h5>
                                
                                <!-- 1. Select Reason -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="step-number">1.</span> Select Reason
                                    </label>
                                    <select class="form-select" name="reason" required>
                                        <option value="">Choose a reason</option>
                                        <option value="WRONG SIZE">Wrong Size</option>
                                        <option value="WRONG ITEM">Wrong Item</option>
                                        <option value="DAMAGED PRODUCT">Damaged Product</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>

                                <!-- 2. Choose Quantity -->
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">
                                        <span class="step-number">2.</span> Choose Quantity
                                    </h6>
                                    <p class="text-muted small mb-3">Please select the items you wish to exchange.</p>
                                    
                                    @if($errors->has('selected_items'))
                                        <div class="alert alert-danger small py-2 mb-3">
                                            {{ $errors->first('selected_items') }}
                                        </div>
                                    @endif

                                    @foreach($order['items'] as $item)
                                        <div class="mb-3 pb-3 border-bottom item-container" data-item-id="{{ $item['id'] }}" style="padding-bottom: 1rem;">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0 pt-2">
                                                    <input class="form-check-input border-secondary item-checkbox" 
                                                        type="checkbox" 
                                                        name="selected_items[]" 
                                                        value="{{ $item['id'] }}" 
                                                        data-product-type="{{ $item['product_type'] }}" 
                                                        data-available-qty="{{ $item['available_for_return'] ?? $item['quantity'] }}"
                                                        @checked(empty($selectedItems) || in_array($item['id'], $selectedItems ?? [])) 
                                                        style="width: 1.3em; height: 1.3em; cursor: pointer;"
                                                        onchange="toggleQuantityInput({{ $item['id'] }})">
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 70px; height: 70px; overflow: hidden;">
                                                        @if($item['image'])
                                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                                                        @else
                                                            <i class="fas fa-image text-muted"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2 flex-column flex-md-row">
                                                        <div>
                                                            <h6 class="mb-1 text-dark fw-bold">{{ $item['name'] }}</h6>
                                                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                                                <span class="text-muted small">Size: {{ $item['size'] }}</span>
                                                                <span class="text-muted small d-none d-sm-inline">•</span>
                                                                <span class="text-muted small">Available: <strong class="text-success">{{ $item['available_for_return'] ?? $item['quantity'] }}</strong></span>
                                                            </div>
                                                        </div>
                                                        <div class="text-end text-md-end mt-2 mt-md-0">
                                                            <div class="text-primary fw-bold">₹{{ number_format($item['price'], 2) }}</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Quantity Selector (only show if quantity > 1) -->
                                                    @if(($item['available_for_return'] ?? $item['quantity']) > 1)
                                                    <div class="quantity-selector mt-3" id="quantity-selector-{{ $item['id'] }}" style="display: block;">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <span class="small fw-semibold text-dark me-2 w-100 w-md-auto">Quantity:</span>
                                                            <div class="d-flex align-items-center gap-2 quantity-controls">
                                                                <button type="button" class="btn quantity-btn-decrement" id="decrement-{{ $item['id'] }}" onclick="decrementQuantity({{ $item['id'] }}, {{ $item['available_for_return'] ?? $item['quantity'] }})">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                                <input type="number" 
                                                                    name="quantities[{{ $item['id'] }}]" 
                                                                    class="form-control quantity-input text-center" 
                                                                    id="quantity-{{ $item['id'] }}"
                                                                    min="1" 
                                                                    max="{{ $item['available_for_return'] ?? $item['quantity'] }}"
                                                                    value="1"
                                                                    onchange="validateQuantity({{ $item['id'] }}, {{ $item['available_for_return'] ?? $item['quantity'] }})"
                                                                    required>
                                                                <button type="button" class="btn quantity-btn-increment" id="increment-{{ $item['id'] }}" onclick="incrementQuantity({{ $item['id'] }}, {{ $item['available_for_return'] ?? $item['quantity'] }})">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                            <small class="text-muted ms-2 ms-md-2 w-100 w-md-auto">(Max: {{ $item['available_for_return'] ?? $item['quantity'] }})</small>
                                                        </div>
                                                        <small class="text-danger d-none" id="quantity-error-{{ $item['id'] }}"></small>
                                                    </div>
                                                    @else
                                                    {{-- Hidden input for single quantity items --}}
                                                    <input type="hidden" name="quantities[{{ $item['id'] }}]" value="1">
                                                    @endif

                                                    <!-- Exchange size (optional) -->
                                                    <div class="mt-3 exchange-size-row" id="exchange-size-row-{{ $item['id'] }}" style="display:block;">
                                                        <label class="small fw-semibold text-dark mb-2 d-block">New Size</label>
                                                        <select class="form-select" id="exchange-size-{{ $item['id'] }}" name="exchange_sizes[{{ $item['id'] }}]">
                                                            <option value="">-- Optional: select new size --</option>
                                                            @foreach(($item['available_sizes'] ?? []) as $sz)
                                                                <option value="{{ $sz }}" @selected(old('exchange_sizes.' . $item['id']) == $sz)>{{ $sz }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted d-block mt-1">Optional. Only choose if you want a different size.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- 3. Add Supporting Photos -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <span class="step-number">3.</span> Add Supporting Photos
                                    </label>
                                    <input type="file" class="form-control" name="photos[]" id="photo-input" accept="image/*" multiple>
                                    <small class="text-muted">You can upload multiple photos to support your exchange request</small>
                                    
                                    <!-- Photo Preview Container -->
                                    <div id="photo-preview-container" class="mt-3" style="display: none;">
                                        <div class="d-flex flex-wrap gap-2" id="photo-preview-list"></div>
                                    </div>
                                </div>

                                <!-- Hidden input for exchange action (already selected by default) -->
                                <input type="hidden" name="action" value="exchange">

                                <!-- Exchange policy info -->
                                <div class="alert alert-warning" style="border-radius: 12px;">
                                    <strong>Important:</strong> Exchange requests must be raised within <strong>48 hours</strong> of the delivery date.
                                </div>

                                <!-- Terms and Service Toggle -->
                                <div class="mb-4">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <label class="toggle-switch-label" style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                                            <input type="checkbox" name="accept_terms" id="accept_terms" value="1" required class="toggle-switch-input">
                                            <span class="toggle-switch-slider"></span>
                                        </label>
                                        <span style="margin: 0;">
                                            I accept the <a href="{{ route('frontend.return-exchange') }}" target="_blank" class="text-primary text-decoration-underline">
                                                Exchange Policy
                                            </a>
                                        </span>
                                    </div>
                                </div>

                                <!-- Size change acknowledgement Toggle -->
                           <!--      <div class="mb-4">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <label class="toggle-switch-label" style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                                            <input type="checkbox" name="accept_size_change" id="accept_size_change" value="1" required class="toggle-switch-input">
                                            <span class="toggle-switch-slider"></span>
                                        </label>
                                        <span style="margin: 0;">
                                            I confirm I am requesting an <strong>exchange</strong> and the <strong>size will be changed</strong> for the selected item(s).
                                        </span>
                                    </div>
                                </div> -->


                                <button type="submit" class="vs-btn" id="submit-btn" disabled>
                                    <i class="fas fa-paper-plane me-2"></i> Submit Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <style>
        .action-option {
            position: relative;
            display: inline-block;
        }

        .action-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .action-option span {
            display: inline-block;
            padding: 10px 30px;
            border: 2px solid #e0d5f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        .action-option input[type="radio"]:checked + span {
            border-color: #490D59;
            background-color: #490D59;
            color: #ffffff;
        }
        
        .action-option input[type="radio"]:disabled + span {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #adb5bd;
            cursor: not-allowed;
        }

        .quantity-input {
            color: #212529 !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            background-color: #ffffff !important;
            border: 2px solid #490D59 !important;
            padding: 10px !important;
            width: 80px !important;
            height: 45px !important;
            border-radius: 8px !important;
        }
        
        .quantity-input:disabled {
            background-color: #f8f9fa !important;
            cursor: not-allowed;
            opacity: 0.6;
            color: #6c757d !important;
            border-color: #ced4da !important;
        }
        
        .quantity-input:focus {
            color: #212529 !important;
            border-color: #490D59 !important;
            box-shadow: 0 0 0 0.2rem rgba(73, 13, 89, 0.25) !important;
            outline: none;
        }
        
        .quantity-btn-decrement,
        .quantity-btn-increment {
            width: 45px !important;
            height: 45px !important;
            padding: 0 !important;
            border-radius: 8px !important;
            background-color: #490D59 !important;
            border: 2px solid #490D59 !important;
            color: #ffffff !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }
        
        .quantity-btn-decrement:hover:not(:disabled),
        .quantity-btn-increment:hover:not(:disabled) {
            background-color: #6a1b7a !important;
            border-color: #6a1b7a !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(73, 13, 89, 0.3);
        }
        
        .quantity-btn-decrement:active:not(:disabled),
        .quantity-btn-increment:active:not(:disabled) {
            transform: scale(0.95);
        }
        
        .quantity-btn-decrement:disabled,
        .quantity-btn-increment:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #ced4da !important;
            border-color: #ced4da !important;
        }
        
        .quantity-controls {
            gap: 0.5rem !important;
        }
        
        .item-container {
            transition: background-color 0.2s ease;
        }
        
        .item-container:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 0.75rem !important;
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }

        .action-option:hover span {
            border-color: #490D59;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .item-container {
                padding-bottom: 1rem !important;
            }
            
            .item-container .d-flex {
                flex-wrap: wrap;
            }
            
            .item-container .flex-grow-1 {
                width: 100%;
                margin-top: 0.75rem;
            }
            
            .item-container .bg-light {
                width: 60px !important;
                height: 60px !important;
            }
            
            .item-container h6 {
                font-size: 0.95rem;
                margin-bottom: 0.5rem;
            }
            
            .item-container .d-flex.justify-content-between {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }
            
            .item-container .text-end {
                text-align: left !important;
                width: 100%;
            }
            
            .quantity-selector {
                margin-top: 1rem !important;
            }
            
            .quantity-selector .d-flex {
                flex-wrap: wrap;
                gap: 0.5rem !important;
            }
            
            .quantity-selector .small.text-muted {
                width: 100%;
                margin-bottom: 0.25rem;
            }
            
            .quantity-selector .quantity-input {
                width: 70px !important;
                font-size: 15px !important;
                height: 42px !important;
            }
            
            .quantity-btn-decrement,
            .quantity-btn-increment {
                width: 42px !important;
                height: 42px !important;
                font-size: 14px !important;
            }
            
            .quantity-selector small.text-muted {
                width: 100%;
                margin-top: 0.5rem;
                margin-left: 0 !important;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
            
            .item-container:hover {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }
        
        @media (max-width: 576px) {
            .card-body {
                padding: 1rem !important;
            }
            
            .item-container {
                padding-bottom: 0.75rem !important;
            }
            
            .item-container .bg-light {
                width: 50px !important;
                height: 50px !important;
            }
            
            .item-container h6 {
                font-size: 0.9rem;
            }
            
            .item-container .text-muted.small {
                font-size: 0.8rem;
            }
            
            .quantity-selector .quantity-input {
                width: 65px !important;
                font-size: 14px !important;
                height: 40px !important;
            }
            
            .quantity-btn-decrement,
            .quantity-btn-increment {
                width: 40px !important;
                height: 40px !important;
                font-size: 13px !important;
            }
            
            .form-select,
            .form-control {
                font-size: 14px;
            }
            
            .action-option span {
                padding: 8px 20px !important;
                font-size: 0.9rem;
            }
            
            h5.mb-4 {
                font-size: 1.1rem;
                margin-bottom: 1rem !important;
            }
            
            h6.fw-bold {
                font-size: 0.95rem;
            }
        }
        
        #submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .form-check-label a {
            color: #423ec8 !important;
            font-weight: 500;
        }
        
        .form-check-label a:hover {
            color: #423ec8 !important;
            text-decoration: underline !important;
        }
        
        /* Step Numbers */
        .step-number {
            display: inline-block;
            width: 28px;
            height: 28px;
            background-color: #490D59;
            color: #ffffff;
            border-radius: 50%;
            text-align: center;
            line-height: 28px;
            font-size: 14px;
            font-weight: 600;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        /* Toggle Switch Styling */
        .toggle-switch-label {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
            flex-shrink: 0;
        }
        
        .toggle-switch-input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-switch-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 26px;
        }
        
        .toggle-switch-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        
        .toggle-switch-input:checked + .toggle-switch-slider {
            background-color: #28a745;
        }
        
        .toggle-switch-input:checked + .toggle-switch-slider:before {
            transform: translateX(24px);
        }
        
        .toggle-switch-input:focus + .toggle-switch-slider {
            box-shadow: 0 0 1px #28a745;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 30px;
            flex-shrink: 0;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 30px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .toggle-switch input:checked + .toggle-slider {
            background-color: #dc3545;
        }
        
        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .toggle-switch input:focus + .toggle-slider {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.35);
        }
        
        .toggle-switch:hover .toggle-slider {
            background-color: #b0b0b0;
        }
        
        .toggle-switch:hover input:checked + .toggle-slider {
            background-color: #c82333;
        }
        
        .toggle-label {
            cursor: pointer;
            user-select: none;
            line-height: 1.5;
            margin: 0 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const acceptTermsCheckbox = document.getElementById('accept_terms');
            const acceptSizeCheckbox = document.getElementById('accept_size_change');
            const submitBtn = document.getElementById('submit-btn');
            
            // Enable/disable submit button based on checkboxes that exist
            if (submitBtn) {
                const updateSubmit = () => {
                    let canSubmit = true;
                    if (acceptTermsCheckbox) {
                        canSubmit = canSubmit && acceptTermsCheckbox.checked;
                    }
                    if (acceptSizeCheckbox) {
                        canSubmit = canSubmit && acceptSizeCheckbox.checked;
                    }
                    submitBtn.disabled = !canSubmit;
                };
                
                if (acceptTermsCheckbox) acceptTermsCheckbox.addEventListener('change', updateSubmit);
                if (acceptSizeCheckbox) acceptSizeCheckbox.addEventListener('change', updateSubmit);
                updateSubmit();
            }
            
            itemCheckboxes.forEach(checkbox => {
                // Show quantity selector when item is checked
                checkbox.addEventListener('change', function() {
                    const itemId = this.value;
                    toggleQuantityInput(itemId);
                });
            });
            
            // Initialize quantity selectors for all items (show them, enable/disable based on checkbox state)
            itemCheckboxes.forEach(checkbox => {
                toggleQuantityInput(checkbox.value);
            });
        });
        
        function toggleQuantityInput(itemId) {
            const checkbox = document.querySelector(`input[value="${itemId}"].item-checkbox`);
            const quantitySelector = document.getElementById(`quantity-selector-${itemId}`);
            const quantityInput = document.getElementById(`quantity-${itemId}`);
            const decrementBtn = document.getElementById(`decrement-${itemId}`);
            const incrementBtn = document.getElementById(`increment-${itemId}`);
            const exchangeSizeSelect = document.getElementById(`exchange-size-${itemId}`);
            
            if (checkbox && checkbox.checked) {
                // Only show quantity selector if it exists (i.e., quantity > 1)
                if (quantitySelector) {
                    quantitySelector.style.display = 'block';
                    if (quantityInput) {
                        quantityInput.required = true;
                        quantityInput.disabled = false;
                        // Set max value from data attribute
                        const maxQty = parseInt(checkbox.getAttribute('data-available-qty')) || 1;
                        quantityInput.max = maxQty;
                    }
                    // Enable buttons
                    if (decrementBtn) decrementBtn.disabled = false;
                    if (incrementBtn) incrementBtn.disabled = false;
                }
                // If quantity selector doesn't exist, it means quantity is 1, so hidden input is already set
                if (exchangeSizeSelect) {
                    exchangeSizeSelect.disabled = false;
                }
            } else {
                // Keep quantity selector visible but disable the input when checkbox is unchecked
                if (quantitySelector) {
                    quantitySelector.style.display = 'block'; // Keep visible
                    if (quantityInput) {
                        quantityInput.required = false;
                        quantityInput.disabled = true;
                        quantityInput.value = 1;
                    }
                    // Disable buttons
                    if (decrementBtn) decrementBtn.disabled = true;
                    if (incrementBtn) incrementBtn.disabled = true;
                }
                if (exchangeSizeSelect) {
                    exchangeSizeSelect.disabled = true;
                    exchangeSizeSelect.value = '';
                }
            }
        }
        
        function incrementQuantity(itemId, maxQty) {
            const quantityInput = document.getElementById(`quantity-${itemId}`);
            if (quantityInput && !quantityInput.disabled) {
                let currentValue = parseInt(quantityInput.value) || 1;
                if (currentValue < maxQty) {
                    quantityInput.value = currentValue + 1;
                    validateQuantity(itemId, maxQty);
                }
            }
        }
        
        function decrementQuantity(itemId, maxQty) {
            const quantityInput = document.getElementById(`quantity-${itemId}`);
            if (quantityInput && !quantityInput.disabled) {
                let currentValue = parseInt(quantityInput.value) || 1;
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                    validateQuantity(itemId, maxQty);
                }
            }
        }
        
        function validateQuantity(itemId, maxQuantity) {
            const quantityInput = document.getElementById(`quantity-${itemId}`);
            const errorElement = document.getElementById(`quantity-error-${itemId}`);
            const value = parseInt(quantityInput.value) || 0;
            
            if (value < 1) {
                quantityInput.value = 1;
                errorElement.textContent = 'Quantity must be at least 1';
                errorElement.classList.remove('d-none');
                return false;
            } else if (value > maxQuantity) {
                quantityInput.value = maxQuantity;
                errorElement.textContent = `Maximum quantity is ${maxQuantity}`;
                errorElement.classList.remove('d-none');
                return false;
            } else {
                errorElement.classList.add('d-none');
                return true;
            }
        }
        
        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            let isValid = true;
            
            checkedBoxes.forEach(checkbox => {
                const itemId = checkbox.value;
                const quantityInput = document.getElementById(`quantity-${itemId}`);
                const maxQty = parseInt(checkbox.getAttribute('data-available-qty')) || 1;
                
                if (quantityInput) {
                    const value = parseInt(quantityInput.value) || 0;
                    if (value < 1 || value > maxQty) {
                        isValid = false;
                        validateQuantity(itemId, maxQty);
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please correct the quantity values before submitting.');
            }
        });
        
        // Multiple Photo Upload Preview
        const photoInput = document.getElementById('photo-input');
        const photoPreviewContainer = document.getElementById('photo-preview-container');
        const photoPreviewList = document.getElementById('photo-preview-list');
        let selectedPhotos = [];
        
        if (photoInput) {
            photoInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                selectedPhotos = [];
                photoPreviewList.innerHTML = '';
                
                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        selectedPhotos.push(file);
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const previewDiv = document.createElement('div');
                            previewDiv.className = 'position-relative';
                            previewDiv.style.cssText = 'width: 100px; height: 100px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                            
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'btn btn-sm btn-danger position-absolute';
                            removeBtn.style.cssText = 'top: 2px; right: 2px; padding: 2px 6px; font-size: 12px; line-height: 1;';
                            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                            removeBtn.onclick = function() {
                                removePhoto(index);
                            };
                            
                            previewDiv.appendChild(img);
                            previewDiv.appendChild(removeBtn);
                            photoPreviewList.appendChild(previewDiv);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
                
                if (selectedPhotos.length > 0) {
                    photoPreviewContainer.style.display = 'block';
                } else {
                    photoPreviewContainer.style.display = 'none';
                }
            });
        }
        
        function removePhoto(index) {
            selectedPhotos.splice(index, 1);
            
            // Update file input
            const dt = new DataTransfer();
            selectedPhotos.forEach(file => dt.items.add(file));
            photoInput.files = dt.files;
            
            // Re-render preview
            photoPreviewList.innerHTML = '';
            selectedPhotos.forEach((file, newIndex) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'position-relative';
                    previewDiv.style.cssText = 'width: 100px; height: 100px; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger position-absolute';
                    removeBtn.style.cssText = 'top: 2px; right: 2px; padding: 2px 6px; font-size: 12px; line-height: 1;';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.onclick = function() {
                        removePhoto(newIndex);
                    };
                    
                    previewDiv.appendChild(img);
                    previewDiv.appendChild(removeBtn);
                    photoPreviewList.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
            
            if (selectedPhotos.length === 0) {
                photoPreviewContainer.style.display = 'none';
            }
        }
    </script>
    @endsection
