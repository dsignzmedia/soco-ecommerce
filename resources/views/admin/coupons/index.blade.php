@extends('admin.layouts.base')

@section('title', 'Global Coupons | The Skool Store')
@section('page_heading', 'Global Coupons')
@section('page_subheading', 'Generate and manage 12-hour free shipping coupons.')

@push('styles')
    <style>
        :root {
            --primary-purple: #490D59;
            --primary-purple-light: #8b23a3;
            --success-green: #10b981;
            --danger-red: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        .page-header {
            background: linear-gradient(135deg, #490D59 0%, #8b23a3 100%);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(73, 13, 89, 0.1), 0 2px 4px -1px rgba(73, 13, 89, 0.06);
        }

        .page-header h4 {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: white;
        }

        .page-header p {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 0;
        }

        .generate-btn {
            background: white;
            color: var(--primary-purple);
            border: none;
            padding: 12px 28px;
            margin-top: 1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
            color: var(--primary-purple);
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-purple);
        }

        .coupon-code-badge {
            font-family: 'SF Mono', 'Roboto Mono', 'Courier New', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary-purple);
            background: linear-gradient(135deg, #f8f5ff 0%, #f0e6ff 100%);
            padding: 8px 16px;
            border-radius: 8px;
            letter-spacing: 1.5px;
            border: 2px solid #e9d5f5;
            display: inline-block;
        }

        .copy-btn {
            background: transparent;
            border: none;
            color: var(--gray-500);
            padding: 6px 10px;
            border-radius: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .copy-btn:hover {
            background: var(--gray-100);
            color: var(--primary-purple);
        }

        .copy-btn.copied {
            color: var(--success-green);
        }

        .status-badge-active {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 2px solid #6ee7b7;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge-expired {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 2px solid #fca5a5;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .data-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .data-table table {
            margin-bottom: 0;
        }

        .data-table thead {
            background: linear-gradient(to bottom, #fafbfc, #f4f5f7);
            border-bottom: 2px solid var(--gray-200);
        }

        .data-table thead th {
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-700);
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--gray-100);
            transition: all 0.2s ease;
        }

        .data-table tbody tr:hover {
            background-color: var(--gray-50);
            transform: scale(1.005);
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table tbody td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
        }

        .delete-btn {
            background: white;
            border: 2px solid var(--gray-200);
            color: var(--gray-500);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .delete-btn:hover {
            background: #fee2e2;
            border-color: var(--danger-red);
            color: var(--danger-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        }

        .usage-badge {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #93c5fd;
            padding: 10px 16px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .usage-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
        }

        .usage-icon {
            background: #3b82f6;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .empty-state {
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-50) 100%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 3px solid var(--gray-200);
        }

        .alert-success-custom {
            background: linear-gradient(135deg, #ecfdf3 0%, #d1fae5 100%);
            border: 2px solid #6ee7b7;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            color: #065f46;
        }

        .alert-success-custom i {
            color: #059669;
        }

        .time-remaining {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 6px;
        }

        .date-time-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .date-primary {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.95rem;
        }

        .time-secondary {
            color: var(--gray-500);
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
            }

            .page-header .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 1rem;
            }

            .page-header h4 {
                font-size: 1.5rem;
            }

            .generate-btn {
                width: 100%;
            }

            .data-table thead th,
            .data-table tbody td {
                padding: 0.75rem 1rem;
            }
        }

        /* Modal Overlay Styles - Ensure proper popup behavior */
        #activeCouponModal {
            display: none !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1055 !important;
            width: 100% !important;
            height: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            outline: 0 !important;
        }

        #activeCouponModal.show {
            display: block !important;
        }

        .modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 1050 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="fas fa-ticket-alt me-2"></i>    Global Coupons</h4>
                <p>Generate and manage 12-hour free shipping promotional codes.</p>
            </div>
            <div>
                <button type="button" class="generate-btn" id="generateCouponBtn">
                    <i class="fas fa-magic me-2"></i> Generate 12-Hour Coupon
                </button>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success-custom alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <strong>Success!</strong> {{ session('status') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="data-table">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Coupon Code</th>
                        <th>Created</th>
                        <th>Expires</th>
                        <th>Status</th>
                        {{-- <th>Usage</th> --}}
                        {{-- <th class="text-end">Actions</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="coupon-code-badge">{{ $coupon->code }}</span>
                                    <button class="copy-btn" onclick="copyToClipboard('{{ $coupon->code }}', this)" title="Copy Code">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="date-time-group">
                                    <span class="date-primary">{{ $coupon->created_at->format('M d, Y') }}</span>
                                    <span class="time-secondary">{{ $coupon->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="date-time-group">
                                    @if($coupon->valid_to)
                                        <span class="date-primary {{ now()->gt($coupon->valid_to) ? 'text-danger' : '' }}">
                                            {{ $coupon->valid_to->format('M d, Y') }}
                                        </span>
                                        <span class="time-secondary {{ now()->gt($coupon->valid_to) ? 'text-danger' : '' }}">
                                            {{ $coupon->valid_to->format('h:i A') }}
                                            @if(now()->lt($coupon->valid_to))
                                                <span class="time-remaining">{{ $coupon->valid_to->diffForHumans() }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">No Expiry</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($coupon->isValid())
                                    <span class="status-badge-active">
                                        <i class="fas fa-check-circle"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="status-badge-expired">
                                        <i class="fas fa-times-circle"></i>
                                        Expired
                                    </span>
                                @endif
                            </td>
                            {{-- <td>
                                <a href="{{ route('master.admin.coupons.show', $coupon->id) }}" class="usage-badge">
                                    <div class="usage-icon">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #1e40af; font-size: 1.1rem;">{{ $coupon->used_count }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">Orders</div>
                                    </div>
                                </a>
                            </td> --}}
                            {{-- <td class="text-end">
                                <form action="{{ route('master.admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this coupon?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" data-bs-toggle="tooltip" title="Delete Coupon">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-ticket-alt fa-3x text-secondary"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2" style="font-size: 1.25rem;">No coupons generated yet</h5>
                                    <p class="text-muted mb-0">Create your first 12-hour free shipping coupon to get started.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($coupons->hasPages())
            <div class="border-top px-4 py-3" style="background-color: var(--gray-50);">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

    <!-- Active Coupon Warning Modal -->
    <div class="modal fade" id="activeCouponModal" tabindex="-1" aria-labelledby="activeCouponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                <div class="modal-header" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-bottom: 3px solid #fca5a5; padding: 1.5rem 2rem;">
                    <h5 class="modal-title fw-bold" id="activeCouponModalLabel" style="color: #991b1b; font-size: 1.4rem;">
                        <i class="fas fa-exclamation-triangle me-2"></i> Active Coupon Exists
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2.5rem;">
                    <div class="text-center mb-4">
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 4px solid #fbbf24; box-shadow: 0 4px 6px -1px rgba(251, 191, 36, 0.3);">
                            <i class="fas fa-ticket-alt fa-3x" style="color: #92400e;"></i>
                        </div>
                        <h6 class="fw-bold mb-3" style="color: #374151; font-size: 1.3rem;">Cannot Generate New Coupon</h6>
                        <p class="text-muted mb-0" style="font-size: 1rem; line-height: 1.6;">An active coupon is already in use. You can only generate a new coupon after the current one expires.</p>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #f8f5ff 0%, #f0e6ff 100%); border-radius: 16px; padding: 2rem; border: 3px solid #e9d5f5; margin-top: 2rem;">
                        <div class="mb-4">
                            <label class="text-muted small mb-2 d-block" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Active Coupon Code</label>
                            <div class="d-flex align-items-center gap-3">
                                <span id="modalCouponCode" class="coupon-code-badge" style="flex-grow: 1; text-align: center; font-size: 1.5rem; padding: 12px 20px;">SOCO12AC</span>
                                <button class="copy-btn" onclick="copyFromModal()" title="Copy Code" style="padding: 10px 14px;">
                                    <i class="far fa-copy fa-lg"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="text-muted small mb-2 d-block" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Expires At</label>
                                <div id="modalExpiresAt" class="fw-bold" style="color: #374151; font-size: 1.1rem;">Feb 08, 2026 at 08:00 PM</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-2 d-block" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Time Remaining</label>
                                <div id="modalExpiresIn" class="fw-bold" style="color: #92400e; font-size: 1.1rem;">
                                    <i class="fas fa-clock me-2"></i>11 hours from now
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f9fafb; border-top: 2px solid #e5e7eb; padding: 1.5rem 2rem;">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal" style="border-radius: 10px; padding: 12px 32px; font-weight: 600;">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                    <button type="button" class="btn btn-danger btn-lg" id="deactivateAndGenerateBtn" style="border-radius: 10px; padding: 12px 32px; font-weight: 600;">
                        <i class="fas fa-ban me-2"></i> Deactivate & Generate New
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text, btnElement) {
            navigator.clipboard.writeText(text).then(function() {
                const icon = btnElement.querySelector('i');
                const originalClass = icon.className;
                
                // Add copied class and change icon
                btnElement.classList.add('copied');
                icon.className = 'fas fa-check';
                
                // Reset after 2 seconds
                setTimeout(function() {
                    btnElement.classList.remove('copied');
                    icon.className = originalClass;
                }, 2000);
            }, function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy coupon code');
            });
        }

        function copyFromModal() {
            const code = document.getElementById('modalCouponCode').textContent;
            navigator.clipboard.writeText(code).then(function() {
                alert('Coupon code copied: ' + code);
            });
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Ensure modal is hidden on page load
            const modalElement = document.getElementById('activeCouponModal');
            if (modalElement) {
                // Remove any show classes that might be present
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                modalElement.setAttribute('aria-hidden', 'true');
                
                // Remove backdrop if it exists
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }

            // AJAX Coupon Generation
            document.getElementById('generateCouponBtn').addEventListener('click', function() {
                const btn = this;
                const originalHTML = btn.innerHTML;
                
                // Disable button and show loading state
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Generating...';
                
                // Make AJAX request
                fetch('{{ route("master.admin.coupons.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    // Handle both success and error responses
                    return response.json().then(data => ({
                        status: response.status,
                        data: data
                    }));
                })
                .then(({status, data}) => {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    
                    if (status === 200 && data.success) {
                        // Success - reload page to show new coupon
                        window.location.reload();
                    } else if (status === 422 || !data.success) {
                        // Show modal with active coupon info
                        document.getElementById('modalCouponCode').textContent = data.coupon.code;
                        document.getElementById('modalExpiresAt').textContent = data.coupon.expires_at;
                        document.getElementById('modalExpiresIn').innerHTML = '<i class="fas fa-clock me-2"></i>' + data.coupon.expires_in;
                        
                        // Store the active coupon ID for deactivation
                        document.getElementById('activeCouponModal').dataset.activeCouponId = data.coupon.id;
                        
                        // Show modal as popup overlay
                        const modalElement = document.getElementById('activeCouponModal');
                        const modal = new bootstrap.Modal(modalElement, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    // Show a user-friendly message without alert
                    const modalElement = document.getElementById('activeCouponModal');
                    document.getElementById('modalCouponCode').textContent = 'ERROR';
                    document.getElementById('modalExpiresAt').textContent = 'Unable to generate coupon';
                    document.getElementById('modalExpiresIn').innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Please try again';
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                });
            });

            // Handle Deactivate & Generate New button
            document.getElementById('deactivateAndGenerateBtn').addEventListener('click', function() {
                const btn = this;
                const originalHTML = btn.innerHTML;
                const modal = bootstrap.Modal.getInstance(document.getElementById('activeCouponModal'));
                const activeCouponId = document.getElementById('activeCouponModal').dataset.activeCouponId;
                
                if (!activeCouponId) {
                    console.error('Unable to identify active coupon ID');
                    return;
                }
                
                // Disable button and show loading
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
                
                // First, deactivate the current coupon
                fetch(`/Master/Admin/coupons/${activeCouponId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ is_active: false })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Now generate a new coupon
                        return fetch('{{ route("master.admin.coupons.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({})
                        });
                    } else {
                        throw new Error('Failed to deactivate coupon');
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal and reload page
                        modal.hide();
                        window.location.reload();
                    } else {
                        throw new Error('Failed to generate new coupon');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    // Keep modal open and show error in console
                    // User can try again or close modal
                });
            });

        });
    </script>
@endsection
