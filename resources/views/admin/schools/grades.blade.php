@extends('admin.layouts.base')

@section('title', 'Grades | ' . $school->name)
@section('page_heading', 'Grades • ' . $school->name)
@section('page_subheading', 'Define the academic ladder so products can be mapped precisely')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; }
        th { font-size: 14px; color:#111827; text-transform:uppercase; letter-spacing:0.05em; }
        td { font-size: 14px; color:#475467; }
    </style>
@endpush

@section('content')
    <div class="card" style="margin-bottom:24px;">
        <form method="POST" action="{{ route('master.admin.schools.grades.store', $school) }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
            @csrf
            <label>
                <span>Grade name *</span>
                <input type="text" name="name" required>
            </label>
            <label>
                <span>Display order *</span>
                <input type="number" name="display_order" min="1" value="1" required>
            </label>
            <label>
                <span>Gender rule *</span>
                <select name="gender_rule">
                    @foreach(['boys','girls','unisex'] as $rule)
                        <option value="{{ $rule }}">{{ ucfirst($rule) }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" style="border:none;background:#490d59;color:#fff;border-radius:12px;font-weight:600;">Add grade</button>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Grade</th>
                    <th>Order</th>
                    <th>Gender</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($grades as $grade)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('master.admin.schools.grades.update', [$school, $grade]) }}" style="display:flex;gap:8px;align-items:center;">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $grade->name }}" style="flex:1;">
                        </td>
                        <td>
                                <input type="number" name="display_order" value="{{ $grade->display_order }}" min="1" style="width:80px;">
                        </td>
                        <td>
                                <select name="gender_rule">
                                    @foreach(['boys','girls','unisex'] as $rule)
                                        <option value="{{ $rule }}" @selected($grade->gender_rule === $rule)>{{ ucfirst($rule) }}</option>
                                    @endforeach
                                </select>
                        </td>
                        <td style="display:flex;gap:8px;align-items:center;">
                                <button type="submit" style="border:none;background:#f0f0ff;color:#490d59;border-radius:8px;padding:6px 12px;">Update</button>
                            </form>
                            <form method="POST" action="{{ route('master.admin.schools.grades.destroy', [$school, $grade]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="border:none;background:#fee4e2;color:#b42318;border-radius:8px;padding:6px 12px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">No grades yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

