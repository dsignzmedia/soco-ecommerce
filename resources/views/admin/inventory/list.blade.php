@extends('admin.layouts.base')

@section('title', 'Inventory List | The Skool Store')
@section('page_heading', 'Inventory List')
@section('page_subheading', 'Master view. Inventory admins can be scoped by school or category later.')

@push('styles')
    <style>
        table { width:100%;border-collapse:collapse; }
        th,td { padding:12px 14px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:13px; }
        th { text-transform:uppercase;letter-spacing:.05em;color:#111827;font-size:12px; }
        .filters { display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px; }
        .filters button, .filters a.reset { border-radius:9999px;font-weight:600;text-align:center; }
        .filters button { border:none;background:#490d59;color:#fff;padding:6px 14px;font-size:12px; }
        .filters a.reset { border:1.5px solid #d0d5dd;color:#475467;padding:5px 14px;font-size:12px; }
    </style>
@endpush

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('master.admin.inventory.dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: 1px solid #d0d5dd; color: #475467; text-decoration: none; font-weight: 500; background: white; transition: all 0.2s;">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
    
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
    
    <section class="card" style="margin-bottom:16px;">
        <form method="GET" class="filters">
            <select name="school_id">
                <option value="">School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status">
                <option value="">Status</option>
                @foreach(['live','draft','archived'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <input type="text" name="q" placeholder="Search product" value="{{ $filters['q'] ?? '' }}">
            <button type="submit">Filter</button>
            <button type="button" onclick="window.location.href='{{ route('master.admin.inventory.list') }}'" class="btn-reset">Reset</button>
        </form>
    </section>

    <section class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Category</th>
                    <th>Current stock</th>
                    <th>Status</th>
                    <th>Last updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="product-row">
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                @if($product->variants && $product->variants->count() > 0)
                                    <button type="button" class="toggle-variants" onclick="toggleVariants({{ $product->id }})" style="background:none; border:none; cursor:pointer; padding:4px; color:#490d59;">
                                        <i class="fas fa-chevron-down" id="icon-{{ $product->id }}"></i>
                                    </button>
                                @endif
                                <div>{{ $product->product_name }}</div>
                            </div>
                        </td>
                        <td>{{ $product->school?->name ?? '—' }}</td>
                        <td>{{ $product->grade?->name ?? 'All' }}</td>
                        <td>{{ $product->category ?? '—' }}</td>
                        <td>
                            <strong>{{ $product->inventory_stock }}</strong>
                            @if($product->variants && $product->variants->count() > 0)
                                <div style="font-size: 11px; color: #6b7280;">
                                    ({{ $product->variants->count() }} variant{{ $product->variants->count() > 1 ? 's' : '' }})
                                </div>
                            @endif
                        </td>
                        <td>{{ ucfirst($product->status) }}</td>
                        <td>{{ optional($product->updated_at)->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('master.admin.inventory.adjust', $product) }}" class="btn-vs-sm">Adjust stock</a>
                        </td>
                    </tr>
                    @if($product->variants && $product->variants->count() > 0)
                    <tr id="variants-{{ $product->id }}" style="display:none; background-color: #f9fafb;">
                        <td colspan="8" style="padding: 0;">
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
                                        <form action="{{ route('master.admin.inventory.variant-stock.update', $product) }}" method="POST" style="display: flex; gap: 8px; align-items: center;" onsubmit="return updateVariantStock(event, {{ $variant->id }})">
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
                    <tr><td colspan="8">No products available.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $products->links() }}
        </div>
    </section>
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
                // If redirected, reload the page to show success message
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
                    const totalStockCell = productRow.querySelector('td:nth-child(5) strong');
                    if (totalStockCell && data.product_total_stock !== undefined) {
                        totalStockCell.textContent = data.product_total_stock;
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

