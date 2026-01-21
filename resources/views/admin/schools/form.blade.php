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

