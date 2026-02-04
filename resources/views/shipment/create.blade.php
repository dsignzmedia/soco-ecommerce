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
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight" class="form-control" required value="{{ old('weight') }}">
                        </div>
                        <div class="col-md-4">
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
