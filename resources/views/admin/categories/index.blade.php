@extends('admin.layouts.base')

@section('title', 'Categories | The Skool Store')
@section('page_heading', 'Categories Management')
@section('page_subheading', 'Manage product categories for better organization')

@push('styles')
    <style>
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

    <div class="card" style="margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <p style="margin:0;color:#475467;">Manage categories used across the platform.</p>
        </div>
        <a href="{{ route('master.admin.categories.create') }}" class="nav__item" style="background:#490d59;color:#fff;border-radius:9999px;padding:8px 16px;width:auto;font-size:13px;font-weight:600;">+ Add Category</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td style="width:70px;">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                        <td>
                            <strong style="color:#111827;">{{ $category->name }}</strong>
                        </td>
                        <td>
                            <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:12px;">{{ $category->slug }}</code>
                        </td>
                        <td>{{ $category->description ?? '—' }}</td>
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
                        <td colspan="7">No categories yet. <a href="{{ route('master.admin.categories.create') }}">Create one</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $categories->links() }}
        </div>
    </div>
@endsection

