@extends('admin.layouts.base')

@section('title', 'Schools | The Skool Store')
@section('page_heading', 'School Management')
@section('page_subheading', 'List + add/edit schools')

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
        .status-pending { background: #fff4e6; color: #b54708; }
        .status-inactive { background: #fef3f2; color: #912018; }
        
        /* Icon button styles */
        .actions .btn-vs-sm {
            min-width: 36px;
            padding: 8px 12px;
        }
        
        .actions .btn-vs-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .actions a.btn-vs-sm:hover {
            background-color: #490d59 !important;
            border-color: #490d59 !important;
            color:rgb(249, 249, 249) !important;
        }
        
        .actions button.btn-vs-sm:hover {
            background: #fee2e2 !important;
            border-color: #f87171 !important;
            color: #b91c1c !important;
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }
        
        /* Tablet Responsive Styles (768px - 1024px) */
        @media (min-width: 768px) and (max-width: 1024px) {
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 12px;
            }
            
            th {
                font-size: 11px;
            }
            
            .card {
                padding: 18px;
            }
            
            /* Hide less important columns on tablet */
            th:nth-child(3), /* City */
            td:nth-child(3),
            th:nth-child(5), /* Grades */
            td:nth-child(5) {
                display: none;
            }
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 767px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            .card {
                padding: 16px;
            }
        }

        /* Custom Pagination Styling (Same as Orders Page) */
        .pagination-container {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
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

        /* Pagination Buttons Container (usually a div with shadow in Tailwind) */
        /* We reset the shadow and rounded corners of the container to apply them to buttons instead */
        .pagination-container nav span[class*="shadow-sm"],
        .pagination-container nav div[class*="shadow-sm"] {
            box-shadow: none !important;
            display: inline-flex;
            gap: 4px; /* Space between buttons */
        }

        /* Pagination Styling - Enhanced (Same as Orders) */
        .pagination {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 0;
            padding: 0;
            list-style: none;
            margin-left: 0;
            margin-right: 0;
        }
        
        .pagination > * {
            margin: 0 !important;
        }
        
        /* All pagination links and spans */
        .pagination-container nav a, 
        .pagination-container nav span[aria-disabled], 
        .pagination-container nav span[aria-current="page"] > span,
        .pagination a,
        .pagination span,
        .pagination .page-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 42px !important;
            height: 42px !important;
            padding: 0 14px !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            border: 1px solid #e5e7eb !important;
            background-color: #ffffff !important;
            color: #6b7280 !important;
            margin: 0 2px !important;
            cursor: pointer;
            box-sizing: border-box !important;
        }
        
        .pagination-container nav a:hover,
        .pagination a:hover,
        .pagination .page-link:hover {
            background-color: #f9fafb !important;
            border-color: #490d59 !important;
            color: #490d59 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(73, 13, 89, 0.15) !important;
        }

        /* Active Page Styles */
        .pagination-container nav span[aria-current="page"] > span,
        .pagination span[aria-current="page"],
        .pagination .active span,
        .pagination .page-item.active .page-link,
        .pagination .page-link.active {
            background-color: #490d59 !important;
            border-color: #490d59 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        /* Disabled State (Previous/Next arrows when inactive) */
        .pagination-container nav span[aria-disabled],
        .pagination span[aria-disabled="true"],
        .pagination .page-item.disabled .page-link,
        .pagination .page-link.disabled {
            background-color: #f3f4f6 !important;
            color: #9ca3af !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
            pointer-events: none;
        }
        
        /* Previous/Next buttons */
        .pagination-container nav a[rel="prev"],
        .pagination-container nav a[rel="next"],
        .pagination a[rel="prev"],
        .pagination a[rel="next"],
        .pagination .page-link[rel="prev"],
        .pagination .page-link[rel="next"] {
            background-color: #ffffff !important;
            border: 1px solid #d1d5db !important;
            color: #490d59 !important;
            font-weight: 600 !important;
            padding: 0 16px !important;
            min-width: auto !important;
        }
        
        .pagination-container nav a[rel="prev"]:hover,
        .pagination-container nav a[rel="next"]:hover,
        .pagination a[rel="prev"]:hover,
        .pagination a[rel="next"]:hover {
            background-color: #490d59 !important;
            color: #ffffff !important;
            border-color: #490d59 !important;
        }
        
        /* Fix for arrows (SVG alignment) */
        .pagination-container nav svg,
        .pagination svg {
            width: 16px;
            height: 16px;
        }
        
        /* Responsive pagination */
        @media (max-width: 768px) {
            .pagination {
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }
            
            .pagination a,
            .pagination span,
            .pagination .page-link,
            .pagination-container nav a,
            .pagination-container nav span {
                min-width: 38px !important;
                height: 38px !important;
                font-size: 13px !important;
                padding: 0 10px !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="card" style="margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; gap: 16px;">
        <div style="flex: 1; min-width: 300px;">
            <p style="margin:0;color:#475467;">Track every partner institution in one table.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form method="GET" style="display: flex; align-items: center; gap: 8px;">
                <select name="status[]" multiple class="filter-input-rounded" style="height: 38px; padding: 0 30px 0 12px; font-size: 13px; min-width: 150px; border: 1px solid #d0d5dd; border-radius: 8px; appearance: none; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236B7280\' stroke-width=\'2\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M19 9l-7 7-7-7\'%3E%3C/path%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 10px center; background-size: 14px;" placeholder="Listing Status">
                    <option value="">All Statuses</option>
                    @foreach(['active', 'pending', 'inactive'] as $st)
                        <option value="{{ $st }}" @selected(in_array($st, (array)request('status')))>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button type="submit" style="padding: 8px 16px; background: #490d59; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">Filter</button>
                <a href="{{ route('master.admin.schools.index') }}" style="padding: 8px 16px; background: white; color: #475467; border: 1px solid #d0d5dd; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">Reset</a>
            </form>
            <a href="{{ route('master.admin.schools.create') }}" class="nav__item" style="background:#490d59;color:#fff;border-radius:9999px;padding:8px 16px;width:auto;font-size:13px;font-weight:600; margin: 0;">+ Add School</a>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>School</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Grades</th>
                    <th>Product Mappings</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $school)
                    <tr>
                        <td style="width:70px;">{{ $loop->iteration }}</td>
                        <td>
                            <strong style="color:#111827; display: block; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $school->name }}">{{ Str::limit($school->name, 45) }}</strong>
                            <div style="font-size:12px;color:#98a2b3;">{{ $school->board ?? 'Board TBD' }}</div>
                        </td>
                        <td>{{ $school->city ?? '—' }}</td>
                        <td>
                            <span class="status-pill status-{{ $school->status }}">{{ $school->status }}</span>
                        </td>
                        <td>{{ $school->grades_count }}</td>
                        <td>{{ $school->product_mappings_count }}</td>
                        <td class="actions">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('master.admin.schools.edit', $school) }}" 
                                   class="btn-vs-sm" 
                                   title="Edit School"
                                   style="padding: 8px 12px; border-radius: 6px; border: 1px solid #d0d5dd; background: white; color: #490d59; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="openDeleteModal('{{ route('master.admin.schools.destroy', $school) }}', '{{ route('master.admin.schools.deletion-stats', $school) }}')" 
                                        class="btn-vs-sm" 
                                        title="Delete School"
                                        style="padding: 8px 12px; border-radius: 6px; background:#fef2f2;color:#ff0000 !important;border:1  px solid #dc2626 !important;cursor:pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>       
                @empty
                    <tr>
                        <td colspan="6">No schools yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-container">
            {{ $schools->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Delete School Modal -->
    <div id="deleteSchoolModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px;">
        <div style="background:#fff;padding:0;border-radius:16px;width:100%;max-width:520px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:modalFadeIn 0.3s ease;">
            
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);padding:24px;border-radius:16px 16px 0 0;text-align:center;flex-shrink:0;">
                <div style="width:64px;height:64px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px);">
                    <i class="fas fa-exclamation-triangle" style="font-size:32px;color:#fff;"></i>
                </div>
                <h3 style="margin:0;font-size:24px;font-weight:700;color:#fff;">Delete School?</h3>
                <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">This action cannot be undone</p>
            </div>

            <!-- Body (Scrollable) -->
            <div style="padding:24px;overflow-y:auto;flex:1;">
                <p style="color:#374151;font-size:15px;line-height:1.6;margin:0 0 20px;text-align:center;">
                    Deleting this school will have the following impact:
                </p>
                
                <!-- Loading State -->
                <div id="statsLoading" style="text-align:center;padding:30px;color:#6b7280;">
                    <div style="display:inline-block;width:40px;height:40px;border:4px solid #f3f4f6;border-top:4px solid #dc2626;border-radius:50%;animation:spin 1s linear infinite;"></div>
                    <p style="margin-top:12px;font-size:14px;">Loading deletion impact...</p>
                </div>
                
                <!-- Stats Display -->
                <div id="statsDisplay" style="display:none;">
                    <!-- Impact Cards -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;text-align:center;">
                            <div style="font-size:28px;font-weight:700;color:#dc2626;margin-bottom:4px;" id="productCount">-</div>
                            <div style="font-size:12px;color:#991b1b;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Products</div>
                        </div>
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;text-align:center;">
                            <div style="font-size:28px;font-weight:700;color:#dc2626;margin-bottom:4px;" id="orderCount">-</div>
                            <div style="font-size:12px;color:#991b1b;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Orders</div>
                        </div>
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;text-align:center;">
                            <div style="font-size:28px;font-weight:700;color:#dc2626;margin-bottom:4px;" id="studentCount">-</div>
                            <div style="font-size:12px;color:#991b1b;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Students</div>
                        </div>
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;text-align:center;">
                            <div style="font-size:28px;font-weight:700;color:#dc2626;margin-bottom:4px;">1</div>
                            <div style="font-size:12px;color:#991b1b;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Admin User</div>
                        </div>
                    </div>

                    <!-- Changes List -->
                    <div style="background:#f9fafb;border-radius:8px;padding:16px;margin-bottom:20px;">
                        <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">
                            <i class="fas fa-list-ul" style="margin-right:6px;color:#dc2626;"></i>Actions to be performed:
                        </div>
                        <ul style="margin:0;padding-left:20px;color:#6b7280;font-size:13px;line-height:2;">
                            <li>Mark school as <strong style="color:#dc2626;">deleted</strong> </li>
                            <li>Archive all associated products</li>
                            <li>Deactivate school admin access</li>
                            <li>Hide from active school listings</li>
                        </ul>
                    </div>
                    
                    <!-- Confirmation Checkbox -->
                    <div style="background:#fff7ed;border:2px solid #fed7aa;border-radius:8px;padding:14px;margin-bottom:20px;">
                        <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;user-select:none;">
                            <input type="checkbox" id="confirmDeleteCheckbox" style="flex-shrink:0;margin-top:3px;width:18px;height:18px;cursor:pointer;accent-color:#dc2626;">
                            <span style="font-size:13px;color:#92400e;line-height:1.5;flex:1;text-align:left;">
                                I understand this action will <strong>permanently remove</strong> the school from the active project view and affect all related data.
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding:16px 24px 24px;display:flex;gap:12px;flex-shrink:0;border-top:1px solid #e5e7eb;background:#fff;border-radius:0 0 16px 16px;">
                <button onclick="closeDeleteModal()" style="flex:1;padding:12px 24px;border:2px solid #d1d5db;background:#fff;border-radius:8px;font-size:15px;font-weight:600;color:#374151;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#fff'">
                    <i class="fas fa-times" style="margin-right:6px;"></i>Cancel
                </button>
                <form id="deleteSchoolForm" method="POST" style="margin:0;flex:1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="confirmDeleteBtn" disabled style="width:100%;padding:12px 24px;border:none;background:#9ca3af;border-radius:8px;font-size:15px;font-weight:600;color:#fff;cursor:not-allowed;transition:all 0.2s;">
                        <i class="fas fa-trash-alt" style="margin-right:6px;"></i>Delete School
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        #confirmDeleteBtn:not([disabled]) {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            cursor: pointer !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }
        
        #confirmDeleteBtn:not([disabled]):hover {
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
            transform: translateY(-1px);
        }
    </style>

    <script>
        const deleteModal = document.getElementById('deleteSchoolModal');
        const deleteForm = document.getElementById('deleteSchoolForm');
        const confirmCheckbox = document.getElementById('confirmDeleteCheckbox');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const statsLoading = document.getElementById('statsLoading');
        const statsDisplay = document.getElementById('statsDisplay');

        function openDeleteModal(deleteUrl, statsUrl) {
            deleteForm.action = deleteUrl;
            deleteModal.style.display = 'flex';
            confirmCheckbox.checked = false;
            confirmBtn.disabled = true;
            
            // Show loading, hide stats
            statsLoading.style.display = 'block';
            statsDisplay.style.display = 'none';
            
            // Fetch deletion statistics
            fetch(statsUrl)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('productCount').textContent = data.products || 0;
                    document.getElementById('orderCount').textContent = data.orders || 0;
                    document.getElementById('studentCount').textContent = data.students || 0;
                    
                    // Hide loading, show stats
                    statsLoading.style.display = 'none';
                    statsDisplay.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching stats:', error);
                    statsLoading.innerHTML = '<span style="color:#dc2626;">Failed to load statistics. Please try again.</span>';
                });
        }

        function closeDeleteModal() {
            deleteModal.style.display = 'none';
        }

        confirmCheckbox.addEventListener('change', function() {
            confirmBtn.disabled = !this.checked;
        });

        // Close on outside click
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
