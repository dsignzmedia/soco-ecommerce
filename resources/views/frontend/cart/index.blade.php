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
                    <div class="card cart-wrapper-card shadow-sm border-0 mb-4" style="background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                        <div class="table-responsive">
                            <table class="table mb-0" style="border-collapse: separate; border-spacing: 0;">
                                <thead>
                                    <tr style="background-color: #f8f5ff; color: #333333;">
                                        <th style="padding: 15px; font-weight: 600; border: none; width: 50px;">
                                            <input type="checkbox" id="selectAll" onchange="toggleAllItems()" style="cursor: pointer;">
                                        </th>
                                        <th style="padding: 15px; font-weight: 600; border: none;">Image</th>
                                        <th style="padding: 15px; font-weight: 600; border: none;">Product Name</th>
                                        <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Price</th>
                                        <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Quantity</th>
                                        <th style="padding: 15px; font-weight: 600; border: none; text-align: right; {{ $hasItemTotals ? '' : 'display:none;' }}" data-role="total-header">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $index => $item)
                                        <tr style="background-color: #ffffff; border-bottom: 1px solid #e9ecef;" class="cart-item-row" data-item-index="{{ $index }}">
                                            <td style="padding: 15px; vertical-align: middle;" class="cart-checkbox">
                                                <input type="checkbox" 
                                                       class="item-checkbox" 
                                                       name="selected_items[]" 
                                                       value="{{ $item['id'] }}"
                                                       onchange="updateOrderSummary()"
                                                       checked
                                                       style="cursor: pointer;">
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle;" data-title="Image">
                                                <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #e9ecef; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="showProductSummary({{ $index }})">
                                                    @if(isset($item['image']) && $item['image'])
                                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <i class="fas fa-image fa-2x text-muted"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; cursor: pointer;" onclick="showProductSummary({{ $index }})" data-title="Product">
                                                <h6 class="mb-1" style="font-weight: 600; color: #333; margin: 0;">{{ $item['name'] }}</h6>
                                                <p class="text-muted small mb-0" style="font-size: 0.875rem; margin: 0;">Size: {{ $item['size'] }}</p>
                                                <p class="text-primary small mb-0" style="font-size: 0.8rem; margin: 0;">Student: {{ $item['student_name'] }}</p>
                                                <!-- Mobile delete button near product name -->
                                                <form action="{{ route('frontend.parent.remove-from-cart') }}" method="POST" class="mobile-delete-btn d-md-none">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                    <button type="submit" class="btn btn-sm" style="background-color: #f8f5ff; color: #dc3545; border: 1px solid #e0d5f0; border-radius: 6px; padding: 6px 10px; transition: all 0.3s ease;" title="Remove item" onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='#ffffff'; this.style.borderColor='#dc3545';" onmouseout="this.style.backgroundColor='#f8f5ff'; this.style.color='#dc3545'; this.style.borderColor='#e0d5f0';">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: center;" data-title="Price">
                                                <span style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['price']) }}</span>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: center;" data-title="Quantity">
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
                                            </td>
                                            @php $showItemTotal = ($item['quantity'] ?? 1) > 1; @endphp
                                            <td style="padding: 15px; vertical-align: middle; text-align: right; {{ $showItemTotal ? '' : 'display:none;' }}" data-title="Total">
                                                <div class="d-flex align-items-center justify-content-end gap-3 mobile-actions">
                                                    <span class="item-total-display" 
                                                          data-item-id="{{ $item['id'] }}"
                                                          data-item-price="{{ $item['price'] }}"
                                                          style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['item_total']) }}</span>
                                                    <form action="{{ route('frontend.parent.remove-from-cart') }}" method="POST" class="d-none d-md-inline">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $item['id'] }}">
                                                        <button type="submit" class="btn btn-sm" style="background-color: #f8f5ff; color: #dc3545; border: 1px solid #e0d5f0; border-radius: 6px; padding: 6px 12px; transition: all 0.3s ease;" title="Remove item" onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='#ffffff'; this.style.borderColor='#dc3545';" onmouseout="this.style.backgroundColor='#f8f5ff'; this.style.color='#dc3545'; this.style.borderColor='#e0d5f0';">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Product Summary Section -->
                    <div class="card shadow-sm border-0 mt-4" style="background-color: #ffffff; border-radius: 12px; display: none;" id="productSummaryCard">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0" style="font-weight: 600; color: #333;">Product Summary</h5>
                                <button type="button" class="btn-close" onclick="hideProductSummary()" style="font-size: 0.8rem;"></button>
                            </div>
                            <div id="productSummaryContent"></div>
                        </div>
                    </div>
                @else
                    <div class="card tune shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                            <h4 class="mb-3">Your cart is empty</h4>
                            <p class="text-muted mb-4">Start shopping to add items to your cart.</p>
                            <a href="{{ route('frontend.parent.dashboard') }}" class="vs-btn">Go to Dashboard</a>
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
    /* Force visibility for checkboxes on all screens */
    input[type="checkbox"].item-checkbox,
    input[type="checkbox"]#selectAll {
        appearance: checkbox !important;
        -webkit-appearance: checkbox !important;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 20px !important;
        height: 20px !important;
        position: relative !important;
        z-index: 10 !important;
        cursor: pointer !important;
    }

@media (min-width: 992px) {
    .order-summary-card {
        position: sticky;
        top: 120px;
    }
}

@media (max-width: 768px) {
    /* Make the wrapper transparent so TRs look like floating cards */
    .cart-wrapper-card {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .table-responsive thead {
        display: none;
    }

    .table-responsive {
        overflow-x: hidden !important;
    }

    .table-responsive tr {
        display: grid;
        grid-template-columns: 45px 70px 1fr;
        column-gap: 10px;
        row-gap: 0;
        background: #fff;
        margin: 0 4px 12px 4px; /* tighter margins */
        padding: 12px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        position: relative;
        border: 1px solid #e9ecef;
    }

    .table-responsive td {
        display: block;
        padding: 0 !important;
        border: none !important;
        font-size: 0.9rem;
        line-height: 1.3;
        overflow: hidden;
    }

    /* Checkbox Column */
    .table-responsive td.cart-checkbox {
        grid-column: 1;
        grid-row: 1 / -1;
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 50px !important;
        min-width: 50px !important;
        z-index: 10;
        position: relative;
    }
    
    .table-responsive td.cart-checkbox input.item-checkbox {
        width: 20px !important;
        height: 20px !important;
        display: block !important;
        visibility: visible !important;
        appearance: checkbox !important;
        -webkit-appearance: checkbox !important;
        opacity: 1 !important;
        position: relative !important;
        z-index: 11 !important;
    }

    /* Image Column */
    .table-responsive td[data-title="Image"] {
        grid-column: 2;
        grid-row: 1 / -1;
    }

    /* Info Column - All details go to column 3 */
    .table-responsive td[data-title="Product"],
    .table-responsive td[data-title="Price"],
    .table-responsive td[data-title="Quantity"],
    .table-responsive td[data-title="Total"] {
        grid-column: 3;
    }

    /* Product Details */
    .table-responsive td[data-title="Product"] {
        margin-bottom: 4px;
        padding-right: 28px !important; /* Space for delete button */
        position: relative;
    }

    /* Price, Quantity, Total Styling */
    .table-responsive td[data-title="Price"]::before,
    .table-responsive td[data-title="Quantity"]::before,
    .table-responsive td[data-title="Total"]::before {
        content: attr(data-title) ": ";
        font-weight: 600;
        color: #666;
        font-size: 0.85rem;
    }

    .table-responsive td[data-title="Price"],
    .table-responsive td[data-title="Quantity"],
    .table-responsive td[data-title="Total"] {
        font-size: 0.88rem;
        margin-bottom: 2px;
        line-height: 1.35;
    }

    /* Price row: label left, value right */
    .table-responsive td[data-title="Price"] {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }
    .table-responsive td[data-title="Price"]::before {
        flex-shrink: 0;
        margin-right: 6px;
    }

    /* Quantity row: label left, controls right */
    .table-responsive td[data-title="Quantity"] {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }
    .table-responsive td[data-title="Quantity"]::before {
        flex-shrink: 0;
        margin-right: 6px;
    }
    .table-responsive td[data-title="Quantity"] .quantity-controls {
        margin: 0;
        padding: 2px 6px;
        gap: 4px;
        width: auto;
        border: 1px solid #e0d5f0;
        border-radius: 8px;
        background-color: #f8f5ff;
    }
    .table-responsive td[data-title="Quantity"] .quantity-controls .quantity-btn {
        width: 24px;
        height: 24px;
    }
    .table-responsive td[data-title="Quantity"] .quantity-controls .quantity-display {
        min-width: 18px;
        font-size: 13px;
    }

    /* Total Column - Keep label and price on same line */
    .table-responsive td[data-title="Total"] {
        position: relative;
        /* padding-right: 50px !important; */
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 8px;
        justify-content: space-between;
    }
    
    .table-responsive td[data-title="Total"]::before {
        white-space: nowrap;
        flex-shrink: 0;
    }
    
    .table-responsive td[data-title="Total"] .mobile-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    
    .table-responsive td[data-title="Total"] .mobile-actions form {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 2;
    }
    
    .table-responsive td[data-title="Total"] .mobile-actions span {
        white-space: nowrap;
    }
    
    /* Mobile delete button near product name */
    .mobile-delete-btn {
        position: absolute;
        top: 8px;
        right: 0;
        z-index: 3;
    }
    
    /* Quantity Controls Styling */
    .quantity-controls {
        justify-content: center;
    }
    
    .quantity-btn:disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
    
    /* Mobile Quantity Controls */
    .table-responsive td[data-title="Quantity"] {
        margin-bottom: 10px;
    }
    
    .quantity-controls {
        width: 100%;
        justify-content: center;
        margin: 5px 0;
    }
    
    .quantity-display {
        font-size: 16px !important;
    }
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

function showProductSummary(index) {
    const item = cartItems[index];
    if (!item) return;
    
    const summaryCard = document.getElementById('productSummaryCard');
    const summaryContent = document.getElementById('productSummaryContent');
    
    summaryContent.innerHTML = `
        <div class="row">
            <div class="col-12 mb-3">
                <div style="width: 100%; max-width: 200px; height: 200px; border-radius: 8px; overflow: hidden; border: 1px solid #e9ecef; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    ${item.image ? `<img src="${item.image}" alt="${item.name}" style="width: 100%; height: 100%; object-fit: cover;">` : '<i class="fas fa-image fa-3x text-muted"></i>'}
                </div>
            </div>
            <div class="col-12">
                <h5 class="mb-2" style="font-weight: 600; color: #333;">${item.name}</h5>
                ${item.description ? `<p class="text-muted mb-2" style="font-size: 0.9rem;">${item.description}</p>` : ''}
                <div class="mb-2">
                    <strong style="color: #333;">Size:</strong> <span style="color: #666;">${item.size}</span>
                </div>
                <div class="mb-2">
                    <strong style="color: #333;">Quantity:</strong> <span style="color: #666;">${item.quantity}</span>
                </div>
                <div class="mb-2">
                    <strong style="color: #333;">Unit Price:</strong> <span style="color: #dc3545; font-weight: 600;">₹${(item.price || 0).toLocaleString('en-IN')}</span>
                </div>
                <div class="mb-2">
                    <strong style="color: #333;">Total:</strong> <span style="color: #dc3545; font-weight: 600; font-size: 1.1rem;">₹${(item.item_total || (item.price * item.quantity)).toLocaleString('en-IN')}</span>
                </div>
                ${item.type ? `<div class="mb-2"><span class="badge" style="background-color: #28a745; color: #fff;">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</span></div>` : ''}
            </div>
        </div>
    `;
    
    summaryCard.style.display = 'block';
    summaryCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function hideProductSummary() {
    document.getElementById('productSummaryCard').style.display = 'none';
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
                // Show or hide total cell based on quantity
                const totalCell = itemTotalDisplay.closest('td[data-title="Total"]');
                if (totalCell) {
                    totalCell.style.display = data.quantity > 1 ? '' : 'none';
                }
            }
            
            // Update cartItems array
            const itemIndex = cartItems.findIndex(item => item.id == itemId);
            if (itemIndex !== -1) {
                cartItems[itemIndex].quantity = data.quantity;
                cartItems[itemIndex].item_total = data.item_total;
                cartItems[itemIndex].price = data.item_price;
            }

            // Toggle total header based on any qty > 1
            const totalHeader = document.querySelector('[data-role="total-header"]');
            const anyTotals = cartItems.some(i => (i.quantity || 1) > 1);
            if (totalHeader) {
                totalHeader.style.display = anyTotals ? '' : 'none';
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

