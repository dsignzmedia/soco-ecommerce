@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="h3 mb-2">Shopping Cart</h2>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(count($cartItems) > 0)
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4" style="background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                        <div class="table-responsive">
                            <table class="table mb-0" style="border-collapse: separate; border-spacing: 0;">
                                <thead>
                                    <tr style="background-color: #f8f5ff; color: #333333;">
                                        <th style="padding: 15px; font-weight: 600; border: none;">Image</th>
                                        <th style="padding: 15px; font-weight: 600; border: none;">Product Name</th>
                                        <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Price</th>
                                        <th style="padding: 15px; font-weight: 600; border: none; text-align: center;">Quantity</th>
                                        <th style="padding: 15px; font-weight: 600; border: none; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $index => $item)
                                        <tr style="background-color: #ffffff; border-bottom: 1px solid #e9ecef;">
                                            <td style="padding: 15px; vertical-align: middle;">
                                                <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #e9ecef; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                    @if(isset($item['image']) && $item['image'])
                                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <i class="fas fa-image fa-2x text-muted"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle;">
                                                <h6 class="mb-1" style="font-weight: 600; color: #333; margin: 0;">{{ $item['name'] }}</h6>
                                                <p class="text-muted small mb-0" style="font-size: 0.875rem; margin: 0;">Size: {{ $item['size'] }}</p>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                                <span style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['price']) }}</span>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                                <span style="font-weight: 500;">{{ str_pad($item['quantity'], 2, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td style="padding: 15px; vertical-align: middle; text-align: right;">
                                                <div class="d-flex align-items-center justify-content-end gap-3">
                                                    <span style="color: #dc3545; font-weight: 600;">₹{{ number_format($item['item_total']) }}</span>
                                                    <form action="{{ route('frontend.parent.remove-from-cart') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="index" value="{{ $index }}">
                                                        <button type="submit" class="btn btn-sm" style="background-color: #f8f5ff; color: #dc3545; border: 1px solid #e0d5f0; border-radius: 6px; padding: 6px 12px; transition: all 0.3s ease;" title="Remove item" onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='#ffffff'; this.style.borderColor='#dc3545';" onmouseout="this.style.backgroundColor='#f8f5ff'; this.style.color='#dc3545'; this.style.borderColor='#e0d5f0';">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0" style="background-color: #ffffff; border-radius: 12px;">
                        <div class="card-body">
                            <h5 class="mb-4" style="font-weight: 600; color: #333;">Order Summary</h5>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span style="color: #666;">Subtotal:</span>
                                <span style="color: #dc3545; font-weight: 600;">₹{{ number_format($total) }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span style="color: #666;">Shipping:</span>
                                </div>
                                <p class="text-muted small mb-0" style="font-size: 0.875rem; text-align: right;">Enter your address to view shipping options.</p>
                            </div>
                            
                            <hr style="margin: 20px 0;">
                            
                            <div class="d-flex justify-content-between mb-4">
                                <strong style="color: #333; font-size: 1.1rem;">Total:</strong>
                                <strong style="color: #dc3545; font-size: 1.1rem;">₹{{ number_format($total) }}</strong>
                            </div>
                            
                            <a href="{{ route('frontend.parent.checkout') }}" class="vs-btn w-100 mb-2" style="text-decoration: none; display: block; text-align: center;">
                                <i class="fas fa-shopping-bag me-2"></i> Proceed to Checkout
                            </a>
                            
                            @if(isset($selectedProfile) && $selectedProfile)
                                <a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id']]) }}" class="vs-btn w-100" style="background: #6c757d; border: none;">
                                    <i class="fas fa-shopping-cart me-2"></i> Buy More Product
                                </a>
                            @else
                                <a href="{{ route('frontend.parent.store') }}" class="vs-btn w-100" style="background: #6c757d; border: none;">
                                    <i class="fas fa-shopping-cart me-2"></i> Buy More Product
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card shadow-sm rounded-4 border-0" style="background-color: #ffffff;">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                    <h4 class="mb-3">Your cart is empty</h4>
                    <p class="text-muted mb-4">Start shopping to add items to your cart.</p>
                    <a href="{{ route('frontend.parent.dashboard') }}" class="vs-btn">Go to Dashboard</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

