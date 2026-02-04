@extends('layouts.dtdc')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Cancel Shipment
            </div>
            <div class="card-body">
                <form action="{{ route('cancel.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">AWB Number</label>
                        <input type="text" name="awb" class="form-control" required placeholder="Enter AWB Number">
                    </div>
                    <button type="submit" class="btn btn-danger">Cancel Shipment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
