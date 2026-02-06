@extends('admin.layouts.base')

@section('title', 'Maintenance Mode | System Settings')
@section('page_heading', 'Maintenance Mode')
@section('page_subheading', 'Manage "Coming Soon" status for site sections.')

@section('content')
    <style>
        /* Custom Toggle Switch Styles */
        .custom-toggle {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .custom-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #490D59; /* Theme Purple */
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #490D59;
        }

        input:checked + .slider:before {
            transform: translateX(24px);
        }
        
        .toggle-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <form action="{{ route('master.admin.settings.maintenance.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="h5 mb-4">Section Visibility</h3>

                    <!-- BTS Field -->
                    <div class="toggle-wrapper">
                        <div>
                            <h6 class="mb-1 fw-bold">Back to School - Coming Soon</h6>
                            <p class="text-muted small mb-0">Show "Coming Soon" popup for BTS section.</p>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="maintenance_bts" value="1" {{ $branding->maintenance_bts ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <!-- Merch Field -->
                    <div class="toggle-wrapper">
                        <div>
                            <h6 class="mb-1 fw-bold">Merchandise - Coming Soon</h6>
                            <p class="text-muted small mb-0">Show "Coming Soon" popup for Merchandise section.</p>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="maintenance_merch" value="1" {{ $branding->maintenance_merch ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="mt-4">
                        <button type="submit" style="width: 100%; padding: 12px; border: none; border-radius: 8px; background-color: #490D59; color: #fff; font-weight: 600; cursor: pointer;">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
