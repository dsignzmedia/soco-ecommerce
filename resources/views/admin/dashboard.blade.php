@extends('admin.layouts.base')

@section('title', 'Master Admin Portal | The Skool Store')
@section('page_heading', 'Master Admin Portal')
@section('page_subheading', 'Full access • Manage. Monitor. Master.')

@push('styles')
    <style>
        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            margin-bottom: 24px;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .filters button,
        .filters a.reset {
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
        }

        .filters button {
            border: none;
            background: #490d59;
            color: #fff;
            padding: 12px 16px;
        }

        .filters a.reset {
            border: 1px solid #d0d5dd;
            color: #475467;
            padding: 12px 16px;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .kpi-card {
            border: 1px solid rgba(15, 23, 42, 0.05);
            border-radius: 16px;
            padding: 18px;
        }

        .kpi-card span {
            display: block;
            font-size: 14px;
            color: #475467;
            margin-bottom: 6px;
        }

        .kpi-card strong {
            font-size: 30px;
            color: #111827;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 18px;
        }

        .chart-card h4 {
            margin: 0 0 12px;
            color: #111827;
        }

        .trend-bars li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .trend-bars .bar {
            flex: 1;
            height: 6px;
            border-radius: 999px;
            background: #f2f4f7;
            position: relative;
        }

        .trend-bars .bar span {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, #490d59, #d946ef);
        }

        .alert-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .alert-item {
            border: 1px solid rgba(15, 23, 42, 0.05);
            border-radius: 14px;
            padding: 16px;
        }

        .alert-item h5 {
            margin: 0 0 8px;
            font-size: 15px;
            color: #111827;
        }

        .alert-item ul {
            margin: 0;
            padding-left: 16px;
            color: #475467;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <section class="card">
        <h3 style="margin:0 0 16px;color:#111827;">Dashboard filters</h3>
        <form class="filters" method="GET">
            <select name="date_range">
                @foreach([
                    'last_7_days' => 'Last 7 days',
                    'last_30_days' => 'Last 30 days',
                    'q1' => 'Quarter to date',
                    'ytd' => 'Year to date'
                ] as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['date_range'] ?? 'last_30_days') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="school_id">
                <option value="">All schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <button type="submit">Apply</button>
            <a class="reset" href="{{ route('master.admin.dashboard') }}">Reset</a>
        </form>
    </section>

    <section class="card">
        <h3 style="margin:0 0 16px;color:#111827;">Key metrics</h3>
        <div class="kpi-grid">
            @foreach($kpis as $kpi)
                <article class="kpi-card">
                    <span>{{ $kpi['label'] }}</span>
                    <strong>{{ $kpi['prefix'] ?? '' }}{{ $kpi['value'] }}</strong>
                </article>
            @endforeach
        </div>
    </section>

    <section class="charts-grid">
        <article class="card chart-card">
            <h4>{{ $charts['salesTrend']['title'] }}</h4>
            <ul class="trend-bars">
                @foreach($charts['salesTrend']['labels'] as $index => $label)
                    @php($value = $charts['salesTrend']['series'][$index])
                    <li>
                        <span style="width:40px;color:#98a2b3;">{{ $label }}</span>
                        <div class="bar"><span style="width:{{ $value }}%;"></span></div>
                        <strong style="width:50px;text-align:right;color:#111827;">₹{{ $value }}</strong>
                    </li>
                @endforeach
            </ul>
        </article>
        <article class="card chart-card">
            <h4>{{ $charts['ordersBySchool']['title'] }}</h4>
            <ul class="trend-bars">
                @foreach($charts['ordersBySchool']['data'] as $row)
                    <li>
                        <span style="width:120px;">{{ $row['label'] }}</span>
                        <div class="bar"><span style="width:{{ $row['value'] }}%;"></span></div>
                        <strong style="width:40px;text-align:right;">{{ $row['value'] }}</strong>
                    </li>
                @endforeach
            </ul>
        </article>
        <article class="card chart-card">
            <h4>{{ $charts['ordersByCategory']['title'] }}</h4>
            <ul class="trend-bars">
                @foreach($charts['ordersByCategory']['data'] as $row)
                    <li>
                        <span style="width:120px;">{{ $row['label'] }}</span>
                        <div class="bar"><span style="width:{{ $row['value'] }}%;"></span></div>
                        <strong style="width:40px;text-align:right;">{{ $row['value'] }}</strong>
                    </li>
                @endforeach
            </ul>
        </article>
        <article class="card chart-card">
            <h4>{{ $charts['stockInsights']['title'] }}</h4>
            <ul class="trend-bars">
                @foreach($charts['stockInsights']['bars'] as $row)
                    <li>
                        <span style="width:120px;">{{ $row['label'] }}</span>
                        <div class="bar"><span style="width:{{ $row['value'] === 0 ? 5 : min(100, $row['value'] * 5) }}%;"></span></div>
                        <strong style="width:40px;text-align:right;">{{ $row['value'] }}</strong>
                    </li>
                @endforeach
            </ul>
        </article>
    </section>

    <section class="card">
        <h3 style="margin:0 0 16px;color:#111827;">Alerts & escalations</h3>
        <ul class="alert-list">
            @foreach($alerts as $alert)
                <li class="alert-item">
                    <h5>{{ $alert['type'] }}</h5>
                    <ul>
                        @foreach($alert['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="card" style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:center;">
        <div>
            <h4 style="margin:0;color:#111827;">Quick links</h4>
            <p style="margin:4px 0 0;color:#475467;">Jump to master modules.</p>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('master.admin.schools.index') }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#490d59;font-weight:600;">School Management</a>
            <a href="{{ route('master.admin.catalog.index') }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#490d59;font-weight:600;">Products & Catalog</a>
        </div>
    </section>
@endsection
