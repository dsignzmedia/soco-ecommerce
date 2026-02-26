@extends('inventoryadmin.layouts.base')

@section('title', 'Inventory List | Inventory Admin')
@section('page_heading', 'Inventory List')
@section('page_subheading', 'Real-time stock levels across all schools')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; }
        td small { color:#98a2b3; display:block; }
        .filters { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; margin-bottom: 20px; }
        .filters button, .filters a.reset { 
            width: 100%; 
            height: 46px !important; 
            border-radius: 12px !important; 
            font-weight: 600; 
            text-align: center; 
            padding: 0 16px; 
            font-size: 14px; 
            transition: all 0.2s; 
            box-sizing: border-box; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer;
        }
        .filters button { 
            border: none; 
            background: #490d59; 
            color: #fff; 
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05); 
        }
        .filters button:hover { background: #370a43; }
        
        .filters a.reset { 
            border: 1px solid #e5e7eb; 
            color: #374151; 
            text-decoration: none; 
            background: #fff; 
        }
        .filters a.reset:hover { 
            background: #f9fafb; 
            border-color: #d1d5db; 
            color: #111827;
        }
        .status-pill { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; text-transform:capitalize; display: inline-block; }
        .status-active { background:#f0fdf4; color:#15803d; }
        .status-inactive { background:#fef2f2; color:#b91c1c; }
    </style>
@endpush

@section('content')
    @if(session('status'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: #d1fae5; color: #065f46; border-radius: 8px; border: 1px solid #10b981;">
            <i class="fas fa-check-circle"></i> {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: #fef2f2; color: #991b1b; border-radius: 8px; border: 1px solid #ef4444;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card" style="margin-bottom:24px;">
        <form class="filters" method="GET">
            <select name="school_id[]" multiple style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;" placeholder="Select School">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(in_array($school->id, (array)($filters['school_id'] ?? [])))>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="product_type[]" multiple style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;" placeholder="Product Type">
                @foreach($productTypes as $type)
                    <option value="{{ $type }}" @selected(in_array($type, (array)($filters['product_type'] ?? [])))>
                        {{ ucwords(str_replace('_', ' ', $type)) }}
                    </option>
                @endforeach
            </select>
            <select name="category[]" multiple style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;" placeholder="Select Category">
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(in_array($category, (array)($filters['category'] ?? [])))>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status[]" multiple style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;" placeholder="Inventory Status">
                <option value="active" @selected(in_array('active', (array)($filters['status'] ?? [])))>Active</option>
                <option value="inactive" @selected(in_array('inactive', (array)($filters['status'] ?? [])))>Inactive</option>
            </select>
            <input type="text" name="q" placeholder="Search Product..." value="{{ $filters['q'] ?? '' }}" style="padding:8px;border:1px solid #e5e7eb;border-radius:8px;">
            <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="submit" style="width: auto; min-width: 120px;">Apply Filter</button>
                <a class="reset" href="{{ route('inventory.admin.inventory.index') }}" style="width: auto; min-width: 100px;">Reset</a>
            </div>
        </form>
    </div>

    <div class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="product-row" data-product-id="{{ $product->id }}">
                        <td>
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
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                @if($product->variants && $product->variants->count() > 0)
                                    <button type="button" class="toggle-variants" onclick="toggleVariants({{ $product->id }})" style="background:none; border:none; cursor:pointer; padding:4px; color:#490d59;">
                                        <i class="fas fa-chevron-down" id="icon-{{ $product->id }}"></i>
                                    </button>
                                @endif
                                <strong>{{ $product->product_name }}</strong>
                            </div>
                        </td>
                        <td>
                            <strong id="product-total-stock-{{ $product->id }}" style="color: {{ $product->inventory_stock <= 0 ? '#b91c1c' : ($product->inventory_stock <= ($product->low_stock_threshold ?? 5) ? '#d97706' : '#15803d') }};font-weight:600;">
                                @if($product->inventory_stock <= 0)
                                    Out of Stock
                                @elseif($product->inventory_stock <= ($product->low_stock_threshold ?? 5))
                                    {{ $product->inventory_stock }} (Low)
                                @else
                                    {{ $product->inventory_stock }}
                                @endif
                            </strong>
                            @if($product->variants && $product->variants->count() > 0)
                                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                    ({{ $product->variants->count() }} variant{{ $product->variants->count() > 1 ? 's' : '' }})
                                </div>
                            @endif
                        </td>
                        <td>{{ $product->school?->name ?? '—' }}</td>
                        <td>{{ $product->grade?->name ?? '—' }}</td>
                        <td>{{ $product->category }}</td>
                        <td>
                            <span class="status-pill status-{{ $product->status }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td>
                            {{ optional($product->updated_at)->format('d M Y') }}
                            <small>{{ optional($product->updated_at)->format('h:i A') }}</small>
                        </td>
                        <td>
                            @if(!$product->variants || $product->variants->count() == 0)
                                <a href="{{ route('inventory.admin.inventory.adjust', $product) }}" class="btn-vs-sm">Adjust Stock</a>
                            @else
                                <span style="color: #9ca3af; font-size: 12px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @if($product->variants && $product->variants->count() > 0)
                    <tr id="variants-{{ $product->id }}" style="display:none; background-color: #f9fafb;">
                        <td colspan="9" style="padding: 0;">
                            <div style="padding: 16px; border-top: 1px solid #e5e7eb;">
                                <h4 style="margin: 0 0 12px; font-size: 13px; font-weight: 700; color: #4b5563; text-transform: uppercase; letter-spacing: 0.05em;">Variant Stock Levels</h4>
                                <div style="display: grid; gap: 12px;">
                                    <table style="width: 100%; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb;">
                                        <thead>
                                            <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                                <th style="padding: 10px 16px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Variant</th>
                                                <th style="padding: 10px 16px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Current Stock</th>
                                                <th style="padding: 10px 16px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Update Stock</th>
                                                <th style="padding: 10px 16px; text-align: right;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->variants as $variant)
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td style="padding: 10px 16px; font-size: 13px; font-weight: 500; color: #111827;">
                                                    {{ $variant->option }}
                                                </td>
                                                <td style="padding: 10px 16px;">
                                                    <span style="display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; 
                                                        background-color: {{ $variant->stock <= 0 ? '#fef2f2' : ($variant->stock <= 5 ? '#fffbeb' : '#ecfdf5') }}; 
                                                        color: {{ $variant->stock <= 0 ? '#b91c1c' : ($variant->stock <= 5 ? '#b45309' : '#047857') }};">
                                                        <span id="current-stock-{{ $variant->id }}">{{ $variant->stock }}</span> Units
                                                    </span>
                                                </td>
                                                <td style="padding: 10px 16px;" colspan="2">
                                                    <form action="{{ route('inventory.admin.inventory.variant-stock.update', $product) }}" method="POST" style="display: flex; gap: 8px; align-items: center; justify-content: flex-end;" onsubmit="return updateVariantStock(event, {{ $variant->id }})">
                                                        @csrf
                                                        <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                                        <input type="number" name="stock" value="{{ $variant->stock }}" min="0" required 
                                                            style="width: 80px; padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; outline: none; transition: border-color 0.2s;" 
                                                            id="stock-input-{{ $variant->id }}"
                                                            onfocus="this.style.borderColor='#490d59'" onblur="this.style.borderColor='#e5e7eb'">
                                                        <button type="submit" class="btn-vs-sm" style="margin:0; height: 32px; font-size: 11px; text-transform:uppercase; letter-spacing:0.5px;">Update</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $products->links() }}
        </div>
    </div>

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
                    const totalStockEl = document.getElementById('product-total-stock-' + productId) || productRow.querySelector('td:nth-child(2) strong');
                    if (totalStockEl && data.product_total_stock !== undefined) {
                        const stock = parseInt(data.product_total_stock);
                        totalStockEl.textContent = stock <= 0 ? 'Out of Stock' : (stock <= 5 ? stock + ' (Low)' : stock);
                        totalStockEl.style.color = stock <= 0 ? '#b91c1c' : (stock <= 5 ? '#d97706' : '#15803d');
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
@endsection
