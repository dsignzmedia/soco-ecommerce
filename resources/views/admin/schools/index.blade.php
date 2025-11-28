@extends('admin.layouts.base')

@section('title', 'Schools | The Skool Store')
@section('page_heading', 'School Management')
@section('page_subheading', 'List + add/edit schools • Grades • Product mapping')

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

        .actions a {
            margin-right: 12px;
            color: #490d59;
            font-weight: 600;
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
        <a href="{{ route('master.admin.schools.create') }}" class="nav__item" style="background:#490d59;color:#fff;border-radius:12px;padding:10px 16px;width:auto;">+ Add School</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
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
                        <td style="width:70px;">{{ $school->id }}</td>
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
                            <a href="{{ route('master.admin.schools.edit', $school) }}">Edit</a>
                            <a href="{{ route('master.admin.schools.grades.index', $school) }}">Grades</a>
                            <a href="{{ route('master.admin.schools.product-mapping.index', $school) }}">Product mapping</a>
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
@endsection

