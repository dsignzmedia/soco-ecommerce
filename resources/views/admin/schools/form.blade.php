@extends('admin.layouts.base')

@php($isEdit = $mode === 'edit')

@section('title', ($isEdit ? 'Edit' : 'Add') . ' School | The Skool Store')
@section('page_heading', $isEdit ? 'Edit School' : 'Add School')
@section('page_subheading', 'Capture campus metadata so products can be mapped accurately')

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">
        <a href="{{ route('master.admin.schools.index') }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to schools
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ $mode === 'edit' ? route('master.admin.schools.update', $school) : route('master.admin.schools.store') }}" enctype="multipart/form-data">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;margin-bottom:24px;">
                <label>
                    <span>School Name *</span>
                    <input type="text" name="name" value="{{ old('name', $school->name) }}" required>
                </label>
                <label>
                    <span>School Logo</span>
                    <input type="file" name="logo" accept="image/*">
                    @if($school->logo)
                        <div style="margin-top:8px;">
                            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" style="height:50px;object-fit:contain;">
                        </div>
                    @endif
                </label>
                <label>
                    <span>Board / Affiliation</span>
                    <input type="text" name="board" value="{{ old('board', $school->board) }}" placeholder="CBSE, ICSE, State Board...">
                </label>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:24px;margin-bottom:24px;">
                <label>
                    <span>Shipping Zone</span>
                    <select name="shipping_zone_id">
                        <option value="">-- Select Zone --</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" @selected(old('shipping_zone_id', $school->shipping_zone_id) == $zone->id)>
                                {{ $zone->name }} @if($zone->cost) - ₹{{ number_format($zone->cost, 2) }}@endif
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>State</span>
                    <input type="text" name="state" value="{{ old('state', $school->state) }}">
                </label>
                <label>
                    <span>Status *</span>
                    <select name="status" required>
                        <option value="active" @selected(old('status', $school->status) === 'active')>Active</option>
                        <option value="pending" @selected(old('status', $school->status) === 'pending')>Pending</option>
                        <option value="inactive" @selected(old('status', $school->status) === 'inactive')>Inactive</option>
                    </select>
                </label>
            </div>

            <div style="margin-top:24px; margin-bottom: 24px;">
                <h3 style="margin:0 0 16px;font-size:16px;font-weight:600;color:#1f2937;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">
                    Promotions & Discounts
                </h3>
                <div style="background-color: #f9fafb; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <div style="margin-bottom:16px;">
                        <label style="cursor:pointer;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                                <span style="font-size: 14px; font-weight: 600; color: #1f2937;">Enable Free Delivery Coupon</span>
                                <div style="position: relative; flex-shrink: 0;">
                                    <input type="hidden" name="coupon_enabled" value="0">
                                    <input type="checkbox" name="coupon_enabled" value="1" id="couponEnabled" 
                                           @checked(old('coupon_enabled', $school->coupon_enabled ?? false))
                                           style="position: absolute; opacity: 0; width: 0; height: 0;">
                                    <div class="toggle-switch" style="width: 48px; height: 24px; background-color: #d1d5db; border-radius: 12px; position: relative; transition: background-color 0.3s;">
                                        <div class="toggle-slider" style="width: 20px; height: 20px; background-color: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: transform 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>
                                    </div>
                                </div>
                            </div>
                            <div style="font-size: 12px; color: #6b7280; line-height: 1.4;">Allow students from this school to use a coupon code for free shipping</div>
                        </label>
                    </div>

                    <div id="couponCodeContainer" style="{{ old('coupon_enabled', $school->coupon_enabled ?? false) ? '' : 'display:none;' }} padding-top: 12px; border-top: 1px solid #e5e7eb;">
                        <label>
                            <span style="font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; display: block;">Coupon Code</span>
                            <input type="text" name="coupon_code" value="{{ old('coupon_code', $school->coupon_code) }}" 
                                   placeholder="e.g. SCHOOLFREESHIP" 
                                   style="max-width: 400px; text-transform: uppercase; font-family: 'Courier New', monospace; font-weight: 600; letter-spacing: 0.5px;">
                            <small style="color:#6b7280;font-size:11px;margin-top:4px;display:block;">
                                <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                                Students of this school can use this code for free shipping at checkout.
                            </small>
                        </label>
                    </div>
                </div>
            </div>

            <h3 style="margin:24px 0 16px;font-size:16px;color:#374151;border-bottom:1px solid #e5e7eb;padding-bottom:8px;">Contact Information</h3>
            
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:24px;margin-bottom:24px;">
                <label>
                    <span>Contact Person</span>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $school->contact_name) }}">
                </label>
                <label>
                    <span>Email</span>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $school->contact_email) }}">
                </label>
                <label>
                    <span>Phone</span>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $school->contact_phone) }}">
                </label>
            </div>

            <label style="margin-bottom:24px;">
                <span>Notes (Internal)</span>
                <textarea name="notes" rows="3">{{ old('notes', $school->notes) }}</textarea>
            </label>
            <div style="margin-top:24px;display:flex;gap:12px;">
                <button type="submit" style="padding:12px 20px;border-radius:12px;border:none;background:#490d59;color:#fff;font-weight:600;">
                    {{ $isEdit ? 'Update School' : 'Create School' }}
                </button>
                <a href="{{ route('master.admin.schools.index') }}" style="padding:12px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const couponCheckbox = document.getElementById('couponEnabled');
        const couponContainer = document.getElementById('couponCodeContainer');
        const toggleSwitch = document.querySelector('.toggle-switch');
        const toggleSlider = document.querySelector('.toggle-slider');

        // Function to update toggle appearance
        function updateToggle(isChecked) {
            if (isChecked) {
                toggleSwitch.style.backgroundColor = '#490D59';
                toggleSlider.style.transform = 'translateX(24px)';
                couponContainer.style.display = 'block';
            } else {
                toggleSwitch.style.backgroundColor = '#d1d5db';
                toggleSlider.style.transform = 'translateX(0)';
                couponContainer.style.display = 'none';
            }
        }

        // Initialize toggle state on page load
        if (couponCheckbox && toggleSwitch) {
            updateToggle(couponCheckbox.checked);

            // Handle toggle click
            toggleSwitch.parentElement.parentElement.addEventListener('click', function(e) {
                e.preventDefault();
                couponCheckbox.checked = !couponCheckbox.checked;
                updateToggle(couponCheckbox.checked);
            });

            // Handle checkbox change (for form validation/reset)
            couponCheckbox.addEventListener('change', function() {
                updateToggle(this.checked);
            });
        }
    });
</script>
@endpush

