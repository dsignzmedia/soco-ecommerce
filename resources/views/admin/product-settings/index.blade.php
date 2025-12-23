<<<<<<< HEAD
@extends($layout ?? 'admin.layouts.base')
=======
@extends('admin.layouts.base')
>>>>>>> 299705238ea0ca997c2d2210725d7c82bc6ed1a2

@section('title', 'Product Settings | The Skool Store')
@section('page_heading', 'Product Settings')
@section('page_subheading', 'Manage product types and categories')

@push('styles')
    <style>
        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 24px;
        }
        .tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.2s;
            margin-bottom: -2px;
        }
        .tab.active {
            color: #490d59;
            border-bottom-color: #490d59;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            font-size: 14px;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        td {
            font-size: 14px;
            color: #475467;
        }
        .status-pill {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-active { background: #ecfdf3; color: #027a48; }
        .status-inactive { background: #fef3f2; color: #912018; }
        
        /* Custom Pagination Styling */
        .pagination-container {
            padding: 12px 20px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            margin-top: 16px;
        }
        
        /* Hide the mobile view (the 'Previous' 'Next' text links on the left) */
        .pagination-container nav > div:first-child {
            display: none !important;
        }

        /* Ensure the desktop view takes full width */
        .pagination-container nav > div:last-child {
            display: flex !important;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }

        /* The "Showing x to y" text */
        .pagination-container p {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }

        /* Pagination Buttons Container */
        .pagination-container nav span[class*="shadow-sm"],
        .pagination-container nav div[class*="shadow-sm"] {
            box-shadow: none !important;
            display: inline-flex;
            gap: 4px;
        }

        /* Common Button Styles */
        .pagination-container nav a, 
        .pagination-container nav span[aria-disabled], 
        .pagination-container nav span[aria-current="page"] > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            width: 36px !important;
            height: 36px !important;
            margin: 0 !important;
            cursor: pointer;
            box-sizing: border-box !important;
        }

        /* Active Page Styles */
        .pagination-container nav span[aria-current="page"] > span {
            background-color: #490d59 !important;
            border-color: #490d59 !important;
            color: white !important;
        }

        /* Disabled State */
        .pagination-container nav span[aria-disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f9fafb;
        }

        /* Hover State for Links */
        .pagination-container nav a:hover {
            background-color: #f9fafb;
            border-color: #d1d5db !important;
            color: #111827;
        }
        
        /* Fix for arrows (SVG alignment) */
        .pagination-container nav svg {
            width: 16px;
            height: 16px;
        }
    </style>
@endpush

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

    <div class="card">
        <div class="tabs">
            <button class="tab {{ !request('tab') || request('tab') === 'product-types' ? 'active' : '' }}" onclick="switchTab('product-types')">
                <i class="fas fa-tags"></i> Product Types
            </button>
            <button class="tab {{ request('tab') === 'categories' ? 'active' : '' }}" onclick="switchTab('categories')">
                <i class="fas fa-folder"></i> Categories
            </button>
        </div>

        <!-- Product Types Tab -->
        <div id="product-types" class="tab-content {{ !request('tab') || request('tab') === 'product-types' ? 'active' : '' }}">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <div>
                    <h3 style="margin:0;color:#111827;">Product Types</h3>
                    <p style="margin:4px 0 0;color:#475467;font-size:14px;">Manage product types used across the platform</p>
                </div>
                <a href="{{ route('master.admin.product-types.create') }}" class="nav__item" style="background:#490d59;color:#fff;border-radius:9999px;padding:8px 16px;width:auto;font-size:13px;font-weight:600;">
                    <i class="fas fa-plus"></i> Add Product Type
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productTypes as $type)
                        <tr>
                            <td style="width:70px;">{{ $loop->iteration + ($productTypes->currentPage() - 1) * $productTypes->perPage() }}</td>
                            <td><strong style="color:#111827;">{{ $type->name }}</strong></td>
                            <td>{{ $type->sort_order }}</td>
                            <td>
                                <span class="status-pill status-{{ $type->is_active ? 'active' : 'inactive' }}">
                                    {{ $type->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="actions">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('master.admin.product-types.edit', $type) }}" class="btn-vs-sm">Edit</a>
                                    <form action="{{ route('master.admin.product-types.destroy', $type) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product type?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-vs-sm" style="background:#b42318;color:#fff;border:none;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No product types yet. <a href="{{ route('master.admin.product-types.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-container">
                {{ $productTypes->onEachSide(1)->links() }}
            </div>
        </div>

        <!-- Categories Tab -->
        <div id="categories" class="tab-content {{ request('tab') === 'categories' ? 'active' : '' }}">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <div>
                    <h3 style="margin:0;color:#111827;">Categories</h3>
                    <p style="margin:4px 0 0;color:#475467;font-size:14px;">Manage product categories for better organization</p>
                </div>
                <a href="{{ route('master.admin.categories.create') }}" class="nav__item" style="background:#490d59;color:#fff;border-radius:9999px;padding:8px 16px;width:auto;font-size:13px;font-weight:600;">
                    <i class="fas fa-plus"></i> Add Category
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td style="width:70px;">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                            <td><strong style="color:#111827;">{{ $category->name }}</strong></td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <span class="status-pill status-{{ $category->is_active ? 'active' : 'inactive' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="actions">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('master.admin.categories.edit', $category) }}" class="btn-vs-sm">Edit</a>
                                    <form action="{{ route('master.admin.categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-vs-sm" style="background:#b42318;color:#fff;border:none;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No categories yet. <a href="{{ route('master.admin.categories.create') }}">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-container">
                {{ $categories->onEachSide(1)->links() }}
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
            
            // Update URL with query parameter to preserve tab state
            const url = new URL(window.location.href);
            if (tabName === 'categories') {
                url.searchParams.set('tab', 'categories');
            } else {
                url.searchParams.set('tab', 'product-types');
            }
            // Remove pagination parameters when switching tabs (reset to page 1)
            url.searchParams.delete('product_types');
            url.searchParams.delete('categories');
            window.location.href = url.toString();
        }
        
        // Initialize tab state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            let activeTab = urlParams.get('tab');
            
            // Backward compatibility: handle old ?categories=1 format
            if (!activeTab && urlParams.get('categories') === '1') {
                activeTab = 'categories';
                // Redirect to new format
                const url = new URL(window.location.href);
                url.searchParams.delete('categories');
                url.searchParams.set('tab', 'categories');
                window.history.replaceState({}, '', url);
            }
            
            activeTab = activeTab || 'product-types';
            
            // Ensure correct tab is active
            if (activeTab === 'categories') {
                const categoriesTab = document.querySelector('.tab:nth-child(2)');
                const productTypesTab = document.querySelector('.tab:nth-child(1)');
                const categoriesContent = document.getElementById('categories');
                const productTypesContent = document.getElementById('product-types');
                
                if (categoriesTab && productTypesTab && categoriesContent && productTypesContent) {
                    productTypesTab.classList.remove('active');
                    productTypesContent.classList.remove('active');
                    categoriesTab.classList.add('active');
                    categoriesContent.classList.add('active');
                }
            }
        });
    </script>
@endsection

