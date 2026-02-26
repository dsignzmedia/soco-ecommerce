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
                <li>Shopping Cart</li>
            </ul>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff; padding-top: 60px;">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>
            
            @php
                $cartIsEmpty = count($cartItems) === 0;
                $contentClass = $cartIsEmpty ? 'col-lg-9' : 'col-lg-6';
                $hasItemTotals = collect($cartItems)->contains(function ($i) {
                    return ($i['quantity'] ?? 1) > 1;
                });
                $hasItemTotals = collect($cartItems)->contains(function ($i) {
                    return ($i['quantity'] ?? 1) > 1;
                });
            @endphp

            <div class="{{ $contentClass }}">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="h3 mb-2">Shopping Cart</h2>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(!$cartIsEmpty)
                    <!-- Select All Checkbox (Hidden) -->
                    <div style="display: none;">
                        <input type="checkbox" id="selectAll" onchange="toggleAllItems()" checked>
                    </div>

                    <!-- Cart Items as Cards -->
                    <div class="cart-items-list">
                        @foreach($cartItems as $index => $item)
                            @php $showItemTotal = ($item['quantity'] ?? 1) > 1; @endphp
                            <div class="card shadow-sm border-0 mb-3 cart-item-row position-relative" 
                                 data-item-index="{{ $index }}"
                                 style="border-radius: 12px; transition: all 0.3s;"
                                 onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(73, 13, 89, 0.15)'"
                                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                                <div class="card-body p-3">
                                    <!-- Hidden Checkbox -->
                                    <input type="checkbox" 
                                           class="item-checkbox" 
                                           name="selected_items[]" 
                                           value="{{ $item['id'] }}"
                                           onchange="updateOrderSummary()"
                                           checked
                                           style="display: none;">

                                    <!-- Product Image and Details Row -->
                                    <div class="row align-items-center">
                                        <!-- Product Image -->
                                        <div class="col-auto">
                                            <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #e9ecef; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                @if(isset($item['image']) && $item['image'])
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Product Details -->
                                        <div class="col">
                                            <h6 class="mb-1" style="font-weight: 600; color: #333; margin: 0; font-size: 1rem;">
                                                {{ $item['name'] }}
                                            </h6>
                                            <div class="mb-2">
                                                <span class="text-muted small me-3">Size: <strong>{{ $item['size'] }}</strong></span>
                                                <span class="text-primary small">Student: <strong>{{ $item['student_name'] }}</strong></span>
                                            </div>
                                            
                                            <!-- Quantity Controls above Price -->
                                            <div class="mb-2">
                                                <div class="quantity-controls" style="display: inline-flex; align-items: center; gap: 8px; border: 1px solid #e0d5f0; border-radius: 8px; padding: 4px; background-color: #f8f5ff;">
                                                    <button type="button" 
                                                            class="quantity-btn decrease-btn" 
                                                            data-item-id="{{ $item['id'] }}"
                                                            data-current-qty="{{ $item['quantity'] }}"
                                                            style="background: #ffffff; border: none; color: #490D59; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                                            onmouseover="this.style.backgroundColor='#490D59'; this.style.color='#ffffff';"
                                                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#490D59';">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="quantity-display" 
                                                          data-item-id="{{ $item['id'] }}"
                                                          style="min-width: 30px; text-align: center; font-weight: 600; color: #333; font-size: 14px;">
                                                        {{ $item['quantity'] }}
                                                    </span>
                                                    <button type="button" 
                                                            class="quantity-btn increase-btn" 
                                                            data-item-id="{{ $item['id'] }}"
                                                            data-current-qty="{{ $item['quantity'] }}"
                                                            style="background: #ffffff; border: none; color: #490D59; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                                            onmouseover="this.style.backgroundColor='#490D59'; this.style.color='#ffffff';"
                                                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#490D59';">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Price and Total -->
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                <div>
                                                    <span class="text-muted small">Price: </span>
                                                    <span style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['price']) }}</span>
                                                </div>
                                                @if($showItemTotal)
                                                    <div class="item-total-container" style="display: block;">
                                                        <span class="text-muted small">Total: </span>
                                                        <span class="item-total-display" 
                                                              data-item-id="{{ $item['id'] }}"
                                                              data-item-price="{{ $item['price'] }}"
                                                              style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['item_total']) }}</span>
                                                    </div>
                                                @else
                                                    <div class="item-total-container" style="display: none;">
                                                        <span class="text-muted small">Total: </span>
                                                        <span class="item-total-display" 
                                                              data-item-id="{{ $item['id'] }}"
                                                              data-item-price="{{ $item['price'] }}"
                                                              style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['item_total']) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Delete Button on Right -->
                                        <div class="col-auto">
                                            <form action="{{ route('frontend.parent.remove-from-cart') }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                <button type="submit" class="btn btn-sm" style="background-color: #f8f5ff; color: #dc3545; border: 1px solid #e0d5f0; border-radius: 6px; padding: 6px 12px; transition: all 0.3s ease;" title="Remove item" onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='#ffffff'; this.style.borderColor='#dc3545';" onmouseout="this.style.backgroundColor='#f8f5ff'; this.style.color='#dc3545'; this.style.borderColor='#e0d5f0';">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="card tune shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                            <h4 class="mb-3">Your cart is empty</h4>
                            <p class="text-muted mb-4">Start shopping to add items to your cart.</p>
                            <a href="{{ route('frontend.parent.store') }}" class="vs-btn">Shop Now</a>
                        </div>
                    </div>
                @endif
            </div>

            @if(!$cartIsEmpty)
                <div class="col-lg-3">
                    <div class="card shadow-sm border-0 order-summary-card mb-4" style="background-color: #ffffff; border-radius: 12px;">
                        <div class="card-body">
                            <h5 class="mb-4" style="font-weight: 600; color: #333;">Order Summary</h5>
                            
                            <div id="selectedItemsSummary" class="mb-3" style="max-height: 200px; overflow-y: auto;"></div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: #666;">Subtotal:</span>
                                <span style="color: #dc3545; font-weight: 600;" id="summarySubtotal">₹{{ number_format($total) }}</span>
                            </div>
                            
                            @if(isset($hasInclusiveTax) && $hasInclusiveTax)
                                <div class="mb-3">
                                    <small style="color: #28a745; font-style: italic;">
                                        <i class="fas fa-check-circle me-1"></i>Inclusive of all tax
                                    </small>
                                </div>
                            @endif
                            
                            <hr style="margin: 20px 0;">
                            
                            <div class="d-flex justify-content-between mb-4">
                                <strong style="color: #333; font-size: 1.1rem;">Total:</strong>
                                <strong style="color: #dc3545; font-size: 1.1rem;" id="summaryTotal">₹{{ number_format($total) }}</strong>
                            </div>
                            
                            <form action="{{ route('frontend.parent.checkout') }}" method="GET" id="checkoutForm" onsubmit="return validateCheckout()">
                                <input type="hidden" name="selected_items" id="selectedItemsInput" value="">
                                <button type="submit" class="vs-btn w-100 mb-2">
                                    <i class="fas fa-shopping-bag me-2"></i> Proceed to Checkout
                                </button>
                            </form>
                            
                            @if(isset($selectedProfile) && $selectedProfile)
                                <a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id']]) }}" class="vs-btn w-100" style="background: #6c757d; border: none;">
                                    <i class="fas fa-shopping-cart me-2"></i> Buy More Product
                                </a>
                            @else
                                <a href="{{ route('frontend.parent.store') }}" class="vs-btn w-100" style="background: #6c757d; border: none;">
                                    <i class="fas fa-shopping-cart me-2"></i> Buy More Product
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<style>
    /* Hide all checkboxes always */
    input[type="checkbox"].item-checkbox,
    input[type="checkbox"]#selectAll {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }

@media (min-width: 992px) {
    .order-summary-card {
        position: sticky;
        top: 120px;
    }
}

@media (max-width: 768px) {
    /* Mobile card adjustments */
    .cart-item-row .card-body {
        padding: 12px !important;
    }
    
    .cart-item-row .row {
        flex-wrap: wrap;
    }
    
    .cart-item-row .col-auto:first-child {
        margin-bottom: 8px;
    }
    
    .cart-item-row .col {
        min-width: 0;
        flex: 1 1 100%;
        margin-bottom: 8px;
    }
    
    .cart-item-row .col-auto:last-child {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .quantity-controls {
        margin: 0;
    }
    
    .quantity-btn:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
}

/* Reduced padding and font for summary buttons */
.order-summary-card .vs-btn {
    padding: 10px 20px !important;
    font-size: 15px !important;
    font-weight: 500 !important;
}

</style>

<script>
// Store cart items data for JavaScript
const cartItems = @json($cartItems);

function toggleAllItems() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    updateOrderSummary();
}

function updateOrderSummary() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    // Update selected items input
    const selectedItemsInput = document.getElementById('selectedItemsInput');
    if (selectedItemsInput) {
        selectedItemsInput.value = selectedIds.join(',');
    }
    
    // Calculate totals for selected items
    let subtotal = 0;
    let selectedItemsHtml = '';
    
    selectedIds.forEach(id => {
        const item = cartItems.find(i => i.id === id);
        if (item) {
            const itemTotal = item.item_total || (item.price * item.quantity);
            subtotal += itemTotal;
            
            selectedItemsHtml += `
                <div class="d-flex gap-2 mb-2 pb-2 border-bottom">
                    <div class="flex-shrink-0">
                        <div style="width: 50px; height: 50px; border-radius: 6px; overflow: hidden; border: 1px solid #e9ecef; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                            ${item.image ? `<img src="${item.image}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover;">` : '<i class="fas fa-image text-muted"></i>'}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0" style="font-size: 0.85rem; font-weight: 600;">${item.name}</h6>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Size: ${item.size} × ${item.quantity}</p>
                        <p class="text-primary small mb-0" style="font-size: 0.75rem;">Student: ${item.student_name}</p>
                        <p class="mb-0" style="color: #dc3545; font-weight: 600; font-size: 0.85rem;">₹${itemTotal.toLocaleString('en-IN')}</p>
                    </div>
                </div>
            `;
        }
    });
    
    // Update summary display
    const summaryContainer = document.getElementById('selectedItemsSummary');
    if (summaryContainer) {
        summaryContainer.innerHTML = selectedItemsHtml || '<p class="text-muted small">No items selected</p>';
    }
    
    const subtotalEl = document.getElementById('summarySubtotal');
    if (subtotalEl) {
        subtotalEl.textContent = '₹' + subtotal.toLocaleString('en-IN');
    }
    
    const totalEl = document.getElementById('summaryTotal');
    if (totalEl) {
        totalEl.textContent = '₹' + subtotal.toLocaleString('en-IN');
    }
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = allChecked;
    }
}


function validateCheckout() {
    const selectedIndices = document.getElementById('selectedItemsInput').value;
    if (!selectedIndices || selectedIndices.trim() === '') {
        alert('Please select at least one item to proceed to checkout.');
        return false;
    }
    return true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateOrderSummary();
    
    // Add event listeners for quantity controls
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const currentQty = parseInt(this.closest('.quantity-controls').querySelector('.quantity-display').textContent);
            const isIncrease = this.classList.contains('increase-btn');
            const newQty = isIncrease ? currentQty + 1 : Math.max(1, currentQty - 1);
            
            updateCartQuantity(itemId, newQty);
        });
    });
});

function updateCartQuantity(itemId, newQuantity) {
    const quantityDisplay = document.querySelector(`.quantity-display[data-item-id="${itemId}"]`);
    const itemTotalDisplay = document.querySelector(`.item-total-display[data-item-id="${itemId}"]`);
    const itemPrice = itemTotalDisplay ? parseFloat(itemTotalDisplay.getAttribute('data-item-price')) : 0;
    const quantityControls = quantityDisplay.closest('.quantity-controls');
    const decreaseBtn = quantityControls.querySelector('.decrease-btn');
    const increaseBtn = quantityControls.querySelector('.increase-btn');
    
    // Disable buttons during update
    decreaseBtn.disabled = true;
    increaseBtn.disabled = true;
    decreaseBtn.style.opacity = '0.5';
    increaseBtn.style.opacity = '0.5';
    decreaseBtn.style.cursor = 'not-allowed';
    increaseBtn.style.cursor = 'not-allowed';
    
    // Show loading state
    const originalQtyText = quantityDisplay.textContent;
    quantityDisplay.textContent = '...';
    
    fetch('{{ route("frontend.parent.update-cart-quantity") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            id: itemId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update quantity display
            quantityDisplay.textContent = data.quantity;
            
            // Update item total (if display element exists)
            if (itemTotalDisplay) {
                const formattedTotal = '₹' + data.item_total.toLocaleString('en-IN');
                itemTotalDisplay.textContent = formattedTotal;
                // Show or hide total container based on quantity
                const totalContainer = itemTotalDisplay.closest('.item-total-container');
                if (totalContainer) {
                    totalContainer.style.display = data.quantity > 1 ? 'block' : 'none';
                }
            }
            
            // Update cartItems array
            const itemIndex = cartItems.findIndex(item => item.id == itemId);
            if (itemIndex !== -1) {
                cartItems[itemIndex].quantity = data.quantity;
                cartItems[itemIndex].item_total = data.item_total;
                cartItems[itemIndex].price = data.item_price;
            }
            
            // Update order summary
            updateOrderSummary();
        } else {
            // Revert on error
            quantityDisplay.textContent = originalQtyText;
            alert(data.message || 'Failed to update quantity. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        quantityDisplay.textContent = originalQtyText;
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        // Re-enable buttons
        decreaseBtn.disabled = false;
        increaseBtn.disabled = false;
        decreaseBtn.style.opacity = '1';
        increaseBtn.style.opacity = '1';
        decreaseBtn.style.cursor = 'pointer';
        increaseBtn.style.cursor = 'pointer';
    });
}
</script>
@endsection

