@extends('admin.layouts.base')

@php($isEdit = $mode === 'edit')

@section('title', ($isEdit ? 'Edit' : 'Add') . ' School | The Skool Store')
@section('page_heading', $isEdit ? 'Edit School' : 'Add School')
@section('page_subheading', 'Capture campus metadata so products can be mapped accurately')

@section('content')
    <div class="card">
        <form method="POST" action="{{ $isEdit ? route('master.admin.schools.update', $school) : route('master.admin.schools.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;">
                <label>
                    <span>Name *</span>
                    <input type="text" name="name" value="{{ old('name', $school->name) }}" required>
                </label>
                <label>
                    <span>Board</span>
                    <input type="text" name="board" value="{{ old('board', $school->board) }}">
                </label>
                <label>
                    <span>City</span>
                    <input type="text" name="city" value="{{ old('city', $school->city) }}">
                </label>
                <label>
                    <span>State</span>
                    <input type="text" name="state" value="{{ old('state', $school->state) }}">
                </label>
                <label>
                    <span>Status *</span>
                    <select name="status">
                        @foreach(['active','pending','inactive'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $school->status ?: 'active') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Contact Name</span>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $school->contact_name) }}">
                </label>
                <label>
                    <span>Contact Email</span>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $school->contact_email) }}">
                </label>
                <label>
                    <span>Contact Phone</span>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $school->contact_phone) }}">
                </label>
            </div>
            <label style="display:block;margin-top:16px;">
                <span>Notes</span>
                <textarea name="notes" rows="4">{{ old('notes', $school->notes) }}</textarea>
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

