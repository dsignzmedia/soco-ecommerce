@extends('layouts.dtdc')

@section('content')
<div class="row justify-content-center mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Track Shipment
            </div>
            <div class="card-body">
                <form action="{{ route('tracking.check') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="awb" class="form-control" placeholder="Enter AWB Number" required value="{{ $awb ?? '' }}">
                        <button class="btn btn-primary" type="submit">Track</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(isset($trackingData))
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Tracking Details: {{ $awb }}
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Current Status: <span class="badge bg-primary">{{ $trackingData['status'] ?? 'Unknown' }}</span></h5>
                    <p class="text-muted">AWB: {{ $trackingData['awb'] ?? $awb }}</p>
                </div>

                <h6>Tracking History</h6>
                @if(isset($trackingData['history']) && is_array($trackingData['history']))
                    <ul class="list-group list-group-flush">
                    @foreach($trackingData['history'] as $event)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $event['status'] ?? 'Update' }}</span>
                            <span class="text-muted small">{{ $event['time'] ?? '' }}</span>
                        </li>
                    @endforeach
                    </ul>
                @else
                    <p>No history available.</p>
                @endif
                
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#rawJson">
                        Debug Raw Data
                    </button>
                    <div class="collapse mt-2" id="rawJson">
                        <pre class="bg-light p-3 border rounded">{{ json_encode($trackingData, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
