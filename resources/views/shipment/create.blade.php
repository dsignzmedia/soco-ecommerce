@extends('layouts.dtdc')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Create New Shipment</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('shipment.store') }}" method="POST">
                    @csrf
                    <div class="mb-4 p-3 bg-light border rounded">
                        <label class="form-label d-block fw-bold">Select Environment:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="environment" id="env_production" value="production" checked>
                            <label class="form-check-label" for="env_production">Production (Live)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="environment" id="env_staging" value="staging">
                            <label class="form-check-label" for="env_staging">Staging (Test)</label>
                        </div>
                    </div>
                    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{!! session('success') !!}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Receiver Name</label>
                            <input type="text" name="receiver_name" class="form-control" required value="{{ old('receiver_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" required value="{{ old('pincode') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" required value="{{ old('city') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" required value="{{ old('state') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" class="form-control" required value="{{ old('weight') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Declared Value</label>
                            <input type="number" step="0.01" name="declared_value" class="form-control" required value="{{ old('declared_value') }}">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Create Shipment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
