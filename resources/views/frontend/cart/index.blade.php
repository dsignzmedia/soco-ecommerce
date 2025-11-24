@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-3 mb-4">
                @include('frontend.dashboard.partials.account-sidebar')
            </div>
            <div class="col-lg-6">
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

                @if(count($cartItems) > 0)
                    <div class="card shadow-sm border-0 mb-4" style="background-color: #ffffff; border-radius: 12px; overflow: hidden;">
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
                                        <th style="padding: 15px; font-weight: 600; border: none; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $index => $item)
                                        <tr style="background-color: #ffffff; border-bottom: 1px solid #e9ecef;" class="cart-item-row" data-item-index="{{ $index }}">
                                            <td style="padding: 15px; vertical-align: middle;">
                                                <input type="checkbox" 
                                                       class="item-checkbox" 
                                                       name="selected_items[]" 
                                                       value="{{ $index }}"
                                                       onchange="updateOrderSummary()"
                                                       checked
                                                       style="cursor: pointer;">
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle;">
                                                <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #e9ecef; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="showProductSummary({{ $index }})">
                                                    @if(isset($item['image']) && $item['image'])
                                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <i class="fas fa-image fa-2x text-muted"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; cursor: pointer;" onclick="showProductSummary({{ $index }})">
                                                <h6 class="mb-1" style="font-weight: 600; color: #333; margin: 0;">{{ $item['name'] }}</h6>
                                                <p class="text-muted small mb-0" style="font-size: 0.875rem; margin: 0;">Size: {{ $item['size'] }}</p>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                                <span style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['price']) }}</span>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                                <span style="font-weight: 500;">{{ str_pad($item['quantity'], 2, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: right;">
                                                <div class="d-flex align-items-center justify-content-end gap-3">
                                                    <span style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['item_total']) }}</span>
                                                    <form action="{{ route('frontend.parent.remove-from-cart') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="index" value="{{ $index }}">
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

            <div class="col-lg-3">
                @if(count($cartItems) > 0)
                    <div class="card shadow-sm border-0 order-summary-card mb-4" style="background-color: #ffffff; border-radius: 12px;">
                        <div class="card-body">
                            <h5 class="mb-4" style="font-weight: 600; color: #333;">Order Summary</h5>
                            
                            <div id="selectedItemsSummary" class="mb-3" style="max-height: 200px; overflow-y: auto;"></div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span style="color: #666;">Subtotal:</span>
                                <span style="color: #dc3545; font-weight: 600;" id="summarySubtotal">₹{{ number_format($total) }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span style="color: #666;">Shipping:</span>
                                </div>
                                <p class="text-muted small mb-0" style="font-size: 0.875rem; text-align: right;">Enter your address to view shipping options.</p>
                            </div>
                            
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
                @endif
            </div>
        </div>
    </div>
</section>

<style>
@media (min-width: 992px) {
    .order-summary-card {
        position: sticky;
        top: 120px;
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
    const selectedIndices = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    // Update selected items input
    document.getElementById('selectedItemsInput').value = selectedIndices.join(',');
    
    // Calculate totals for selected items
    let subtotal = 0;
    let selectedItemsHtml = '';
    
    selectedIndices.forEach(index => {
        const item = cartItems[index];
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
                        <p class="mb-0" style="color: #dc3545; font-weight: 600; font-size: 0.85rem;">₹${itemTotal.toLocaleString('en-IN')}</p>
                    </div>
                </div>
            `;
        }
    });
    
    // Update summary display
    document.getElementById('selectedItemsSummary').innerHTML = selectedItemsHtml || '<p class="text-muted small">No items selected</p>';
    document.getElementById('summarySubtotal').textContent = '₹' + subtotal.toLocaleString('en-IN');
    document.getElementById('summaryTotal').textContent = '₹' + subtotal.toLocaleString('en-IN');
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
    document.getElementById('selectAll').checked = allChecked;
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
});
</script>
@endsection

