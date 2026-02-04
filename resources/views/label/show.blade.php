@extends('layouts.dtdc')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-header">
                Shipment Label
            </div>
            <div class="card-body">
                <h5 class="card-title">Reference: {{ $reference }}</h5>
                <p class="card-text">Click below to download the shipping label.</p>
                <a href="{{ route('label.generate', $reference) }}" class="btn btn-primary">Download Label (PDF)</a>
                <a href="{{ route('shipment.create') }}" class="btn btn-secondary ms-2">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
