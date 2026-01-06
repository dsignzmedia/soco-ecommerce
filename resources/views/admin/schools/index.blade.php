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
    </style>
@endpush

@section('content')
    <div class="card" style="margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
        <div>
            <p style="margin:0;color:#475467;">Track every partner institution in one table.</p>
        </div>
        <a href="{{ route('master.admin.schools.create') }}" class="nav__item" style="background:#490d59;color:#fff;border-radius:9999px;padding:8px 16px;width:auto;font-size:13px;font-weight:600;">+ Add School</a>
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
                            <strong style="color:#111827;">{{ $school->name }}</strong>
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
                                <a href="{{ route('master.admin.schools.edit', $school) }}" class="btn-vs-sm">Edit</a>
                                <button onclick="openDeleteModal('{{ route('master.admin.schools.destroy', $school) }}', '{{ route('master.admin.schools.deletion-stats', $school) }}')" 
                                        class="btn-vs-sm" 
                                        style="background:#fef2f2;color:#dc2626 !important;border:1px solid #fecaca;cursor:pointer;">
                                    Delete
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
        <div style="margin-top:16px;">
            {{ $schools->links() }}
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
