@extends('admin.layouts.merchandise')

@section('title', 'Inventory | Merchandise Admin')
@section('page_heading', 'Inventory Management')
@section('page_subheading', 'Manage stock levels for your products.')

@section('content')
    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: #d1fae5; color: #065f46; border-radius: 8px; border: 1px solid #10b981;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: #fef2f2; color: #991b1b; border-radius: 8px; border: 1px solid #ef4444;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom: 16px;">
        <a href="{{ route('admin.merchandise.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: 1px solid #d0d5dd; color: #475467; text-decoration: none; font-weight: 500; background: white; transition: all 0.2s;">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>

<section class="card" style="margin-bottom:16px;">
    <form method="GET" class="filters">
        <select name="category">
            <option value="">Category</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <select name="product_type">
            <option value="">Product Type (All)</option>
            @foreach($productTypes as $type)
                <option value="{{ $type }}" @selected(($filters['product_type'] ?? '') === $type)>{{ ucwords(str_replace('_', ' ', $type)) }}</option>
            @endforeach
        </select>
        <select name="status">
            <option value="">Status</option>
            @foreach(['live','draft','archived'] as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <input type="text" name="q" placeholder="Search product" value="{{ $filters['q'] ?? '' }}">
        <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="submit" style="width: auto; min-width: 120px;">Filter</button>
            <button type="button" onclick="window.location.href='{{ route('admin.merchandise.inventory.index') }}'" class="btn-reset" style="width: auto; min-width: 100px;">Reset</button>
        </div>
    </form>
</section>

<section class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding:0;">
    <div style="overflow-x:auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Image</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Product Name</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Category</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">School</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:center; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Current Stock</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Update Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr style="border-bottom: 1px solid #e5e7eb;" class="product-row" data-product-id="{{ $product->id }}">
                    <td style="padding: 12px 14px; vertical-align: middle;">
                        @if($product->featured_image)
                            <img src="{{ asset('storage/' . $product->featured_image) }}" 
                                 alt="Img" 
                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;"
                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                        @else
                            <img src="{{ asset('assets/img/no image/no_image.png') }}" 
                                 alt="Default" 
                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;">
                        @endif
                    </td>
                    <td style="padding: 12px 14px; vertical-align: middle;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if($product->variants && $product->variants->count() > 0)
                                <button type="button" class="toggle-variants" onclick="toggleVariants({{ $product->id }})" style="background:none; border:none; cursor:pointer; padding:4px; color:#490d59;">
                                    <i class="fas fa-chevron-down" id="icon-{{ $product->id }}"></i>
                                </button>
                            @endif
                            <div>
                                <div style="font-weight: 500; color: #111827;">{{ $product->product_name }}</div>
                                @if($product->inventory_stock <= 5)
                                    <span style="font-size: 11px; color: #b91c1c; background: #fef2f2; padding: 2px 6px; border-radius: 4px; font-weight: 500;">Low Stock</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding: 12px 14px; vertical-align: middle; color: #4b5563;">{{ $product->category }}</td>
                    <td style="padding: 12px 14px; vertical-align: middle; color: #4b5563;">{{ optional($product->school)->name ?? 'All' }}</td>
                    <td style="padding: 12px 14px; vertical-align: middle; text-align:center;">
                        <strong style="font-size: 14px; color: {{ $product->inventory_stock <= 0 ? '#ef4444' : '#10b981' }};" id="product-total-stock-{{ $product->id }}">
                            {{ $product->inventory_stock }}
                        </strong>
                        @if($product->variants && $product->variants->count() > 0)
                            <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                ({{ $product->variants->count() }} variant{{ $product->variants->count() > 1 ? 's' : '' }})
                            </div>
                        @endif
                    </td>
                    <td style="padding: 12px 14px; vertical-align: middle;">
                        @if($product->variants && $product->variants->count() > 0)
                            <span style="font-size: 12px; color: #6b7280;">Update variants below</span>
                        @else
                            <form action="{{ route('admin.merchandise.inventory.update', $product) }}" method="POST" style="display:flex; gap:8px; align-items:center;">
                                @csrf
                                @method('PUT')
                                <input type="number" name="inventory_stock" value="{{ $product->inventory_stock }}" min="0" style="width: 100px; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                <button type="submit" class="btn-vs-sm" style="margin:0; height: 34px; padding: 0 12px; cursor: pointer;">Save</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @if($product->variants && $product->variants->count() > 0)
                <tr id="variants-{{ $product->id }}" style="display:none; background-color: #f9fafb;">
                    <td colspan="5" style="padding: 0;">
                        <div style="padding: 16px; border-top: 1px solid #e5e7eb;">
                            <h4 style="margin: 0 0 12px; font-size: 14px; font-weight: 600; color: #111827;">Variants Stock</h4>
                            <div style="display: grid; gap: 12px;">
                                @foreach($product->variants as $variant)
                                <div style="display: flex; align-items: center; gap: 12px; padding: 10px; background: white; border: 1px solid #e5e7eb; border-radius: 8px;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 500; color: #111827; margin-bottom: 4px;">Size: {{ $variant->option }}</div>
                                        <div style="font-size: 12px; color: #6b7280;">
                                            Current: <strong style="color: {{ $variant->stock <= 0 ? '#ef4444' : ($variant->stock <= 5 ? '#f59e0b' : '#10b981') }};" id="current-stock-{{ $variant->id }}">{{ $variant->stock }}</strong>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.merchandise.inventory.variant-stock.update', $product) }}" method="POST" style="display: flex; gap: 8px; align-items: center;" onsubmit="return updateVariantStock(event, {{ $variant->id }})">
                                        @csrf
                                        <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                        <input type="number" name="stock" value="{{ $variant->stock }}" min="0" required style="width: 100px; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" id="stock-input-{{ $variant->id }}">
                                        <button type="submit" class="btn-vs-sm" style="margin:0; height: 34px; padding: 0 12px; cursor: pointer;">Update</button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color: #6b7280;">
                        No products found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container p-3">
        {{ $products->links() }}
    </div>
</section>

@push('styles')
<style>
    .filters { display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px; }
    .filters select, .filters input[type="text"] {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        color: #374151;
    }
    .filters select:focus, .filters input[type="text"]:focus {
        outline: none;
        border-color: #490d59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }
    .filters button, .filters .btn-reset { 
        border-radius:9999px;
        font-weight:600;
        text-align:center;
        padding:8px 16px;
        font-size:13px;
        cursor: pointer;
        border: none;
    }
    .filters button { 
        background:#490d59;
        color:#fff;
    }
    .filters button:hover {
        background:#3b0a48;
    }
    .filters .btn-reset { 
        border:1.5px solid #d0d5dd;
        color:#475467;
        background: white;
        text-decoration: none;
        display: inline-block;
    }
    .filters .btn-reset:hover {
        border-color:#b0b5bd;
        background:#f9fafb;
    }
    tr:hover { background-color: #f9fafb; }
    .product-row:hover { background-color: #f9fafb; }
    
    /* Reuse pagination styles from orders page */
    .pagination-container nav > div:first-child { display: none; }
    .pagination-container nav > div:last-child { display: flex; justify-content: space-between; width: 100%; align-items: center; }
    .pagination-container nav span[class*="shadow-sm"] { box-shadow: none !important; display: inline-flex; gap: 4px; }
    .pagination-container nav a, .pagination-container nav span[aria-disabled], .pagination-container nav span[aria-current="page"] > span {
        display: inline-flex; align-items: center; justify-content: center; padding: 0 !important;
        border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #fff;
        color: #374151; font-size: 13px; font-weight: 500; text-decoration: none;
        width: 36px !important; height: 36px !important; margin: 0 !important; cursor: pointer;
    }
    .pagination-container nav span[aria-current="page"] > span {
        background-color: #490d59 !important; border-color: #490d59 !important; color: white !important;
    }
</style>
<script>
    function toggleVariants(productId) {
        const row = document.getElementById('variants-' + productId);
        const icon = document.getElementById('icon-' + productId);
        if (row.style.display === 'none') {
            row.style.display = '';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            row.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }
    
    function updateVariantStock(event, variantId) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const stockValue = formData.get('stock');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        const stockInput = document.getElementById('stock-input-' + variantId);
        
        // Validate stock value
        if (stockValue === '' || parseInt(stockValue) < 0) {
            alert('Please enter a valid stock quantity (0 or greater).');
            return false;
        }
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';
        stockInput.disabled = true;
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                // Update the current stock display
                const currentStockEl = document.getElementById('current-stock-' + variantId);
                if (currentStockEl) {
                    currentStockEl.textContent = data.variant_stock;
                    // Update color based on stock level
                    const stock = parseInt(data.variant_stock);
                    if (stock <= 0) {
                        currentStockEl.style.color = '#ef4444';
                    } else if (stock <= 5) {
                        currentStockEl.style.color = '#f59e0b';
                    } else {
                        currentStockEl.style.color = '#10b981';
                    }
                }
                
                // Update product total stock display if visible
                const productRow = form.closest('tr').previousElementSibling;
                if (productRow) {
                    const productId = productRow.dataset.productId || productRow.querySelector('[data-product-id]')?.dataset.productId;
                    const totalStockEl = document.getElementById('product-total-stock-' + productId) || productRow.querySelector('td:nth-child(4) strong');
                    if (totalStockEl && data.product_total_stock !== undefined) {
                        totalStockEl.textContent = data.product_total_stock;
                        // Update color
                        const stock = parseInt(data.product_total_stock);
                        totalStockEl.style.color = stock <= 0 ? '#ef4444' : '#10b981';
                    }
                }
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:white;padding:12px 20px;border-radius:8px;z-index:10000;box-shadow:0 4px 6px rgba(0,0,0,0.1);';
                successMsg.textContent = data.message || 'Stock updated successfully!';
                document.body.appendChild(successMsg);
                setTimeout(() => successMsg.remove(), 3000);
                
                // Re-enable button and input
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                stockInput.disabled = false;
            } else if (data && !data.success) {
                // Handle error response
                alert(data.message || 'Error updating stock. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                stockInput.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating stock. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            stockInput.disabled = false;
        });
        
        return false;
    }
</script>
@endpush
@endsection
