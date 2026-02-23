@extends('admin.layouts.base')

@section('title', ($mode === 'create' ? 'Add Global Coupon' : 'Edit Coupon') . ' | The Skool Store')
@section('page_heading', $mode === 'create' ? 'Add Global Coupon' : 'Edit Coupon')
@section('page_subheading', 'Coupons can be used globally by any user for free shipping or discounts.')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ $mode === 'create' ? route('master.admin.coupons.store') : route('master.admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <div class="card-body" style="padding: 24px;">
            <div class="mb-4">
                <label class="form-label" style="font-weight: 600; color: #374151;">Coupon Code <span style="color: #dc2626;">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code) }}" 
                       placeholder="e.g. SOCOAF3" 
                       style="text-transform: uppercase; font-family: 'Courier New', monospace; font-weight: 700; letter-spacing: 1px;" required>
                @error('code')
                    <div style="color: #dc2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #374151;">Coupon Type <span style="color: #dc2626;">*</span></label>
                    <select name="type" class="form-select" id="couponType" onchange="toggleFields()" required>
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="free_shipping" {{ old('type', $coupon->type) === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                    </select>
                </div>
                
                <div class="col-md-6" id="discountValueContainer">
                    <div id="fixedAmountField" style="display: {{ old('type', $coupon->type) === 'fixed' ? 'block' : 'none' }};">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Discount Amount (₹)</label>
                        <input type="number" name="discount_amount" class="form-control" value="{{ old('discount_amount', $coupon->discount_amount) }}" step="0.01" min="0">
                    </div>
                    
                    <div id="percentageField" style="display: {{ old('type', $coupon->type) === 'percentage' ? 'block' : 'none' }};">
                        <label class="form-label" style="font-weight: 600; color: #374151;">Discount Percentage (%)</label>
                        <input type="number" name="discount_percentage" class="form-control" value="{{ old('discount_percentage', $coupon->discount_percentage) }}" min="0" max="100">
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #374151;">Valid From</label>
                    <input type="datetime-local" name="valid_from" class="form-control" value="{{ old('valid_from', $coupon->valid_from ? $coupon->valid_from->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight: 600; color: #374151;">Valid To</label>
                    <input type="datetime-local" name="valid_to" class="form-control" value="{{ old('valid_to', $coupon->valid_to ? $coupon->valid_to->format('Y-m-d\TH:i') : '') }}">
                    <small style="color: #6b7280;">Leave empty for no expiry.</small>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" style="font-weight: 600; color: #374151;">Usage Limit (Optional)</label>
                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
                <small style="color: #6b7280;">Max number of times this coupon can be used in total.</small>
            </div>

            <div class="mb-4">
                <label class="form-label" style="font-weight: 600; color: #374151;">Description (Internal Note)</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $coupon->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-check-label" style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                    <span style="font-weight: 600; color: #374151;">Is Active?</span>
                </label>
            </div>

            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('master.admin.coupons.index') }}" class="btn" style="background: #fff; border: 1px solid #d1d5db; color: #374151; padding: 10px 20px; border-radius: 8px; text-decoration: none;">Cancel</a>
                <button type="submit" class="btn" style="background: #490d59; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600;">{{ $mode === 'create' ? 'Create Coupon' : 'Update Coupon' }}</button>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('couponType').value;
        const fixedField = document.getElementById('fixedAmountField');
        const percentageField = document.getElementById('percentageField');
        const valueContainer = document.getElementById('discountValueContainer');

        if (type === 'fixed') {
            fixedField.style.display = 'block';
            percentageField.style.display = 'none';
            valueContainer.style.display = 'block';
        } else if (type === 'percentage') {
            fixedField.style.display = 'none';
            percentageField.style.display = 'block';
            valueContainer.style.display = 'block';
        } else {
            // Free Shipping
            fixedField.style.display = 'none';
            percentageField.style.display = 'none';
            // valueContainer.style.display = 'none'; // Optional: hide container if empty
        }
    }

    // Initial run
    document.addEventListener('DOMContentLoaded', toggleFields);
</script>
@endsection
