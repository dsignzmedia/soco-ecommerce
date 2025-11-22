@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="vs-product-wrapper product-details space-top space-extra-bottom" style="background-color: #f8f5ff;">
    <div class="container">
        <div class="row gx-60">
            <!-- Left: Product Images -->
            <div class="col-lg-6">
                <div class="product-image-gallery">
                    <!-- Thumbnail Images (Left Side) -->
                    <div class="product-thumbnails-vertical">
                        @php
                            $productImages = $product['images'] ?? [$product['image'] ?? asset('assets/img/product/product1-1.png')];
                        @endphp
                        @foreach($productImages as $index => $image)
                            <div class="thumb-item {{ $index === 0 ? 'active' : '' }}" data-image="{{ $image }}" onmouseenter="changeMainImage('{{ $image }}')">
                                <img src="{{ $image }}" alt="{{ $product['name'] }} - Image {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Main Image -->
                    <div class="product-big-img">
                        <div class="main-image-container">
                            <img id="mainProductImage" src="{{ $product['image'] ?? asset('assets/img/product/product1-1.png') }}" alt="{{ $product['name'] }}">
                        </div>
                        
                        <!-- Action Buttons (Centered Below Image) -->
                        <div class="product-actions-center-wrapper">
                            <div class="product-actions-center">
                                <form action="{{ route('frontend.parent.add-to-cart') }}" method="POST" id="addToCartForm" class="d-inline-block me-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                    <input type="hidden" name="profile_id" value="{{ $selectedProfile['id'] }}">
                                    <input type="hidden" name="size" id="cart-size" required>
                                    <input type="hidden" name="quantity" id="cart-quantity" value="1" required>
                                    <button type="submit" class="vs-btn" style="background-color: #ff6b35; min-width: 180px;">
                                        <i class="far fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </form>
                                
                                <form action="{{ route('frontend.parent.buy-now') }}" method="POST" id="buyNowForm" class="d-inline-block">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                    <input type="hidden" name="profile_id" value="{{ $selectedProfile['id'] }}">
                                    <input type="hidden" name="size" id="buy-now-size" required>
                                    <input type="hidden" name="quantity" id="buy-now-quantity" value="1" required>
                                    <button type="submit" class="vs-btn" style="background-color: #dc3545; min-width: 180px;" id="buy-now-btn" disabled>
                                        <i class="fas fa-bolt"></i> Buy Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Information -->
            <div class="col-lg-6 align-self-center">
                <div class="product-about">
                    <h2 class="product-title">{{ $product['name'] }}</h2>
                    
                    <p class="product-price">₹{{ number_format($product['price']) }}</p>
                    <p class="text-muted small mb-4">Inclusive of all Taxes</p>

                    <form action="{{ route('frontend.parent.add-to-cart') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="profile_id" value="{{ $selectedProfile['id'] }}">
                        
                        <!-- Size Selection -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <label class="form-label fw-bold mb-0">Size:</label>
                                <a href="#" class="text-primary small" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" style="text-decoration: underline;">Size Guide</a>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($product['sizes'] as $size)
                                    <label class="size-option">
                                        <input type="radio" name="size" value="{{ $size }}" required>
                                        <span>{{ $size }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Quantity Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2">Quantity:</label>
                            <select name="quantity" id="quantity" class="form-select" style="max-width: 150px;" required>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ $i === 1 ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                    </form>

                    <!-- Description -->
                    <div class="product-description mt-4">
                        <h5 class="mb-3">Description</h5>
                        <p class="product-text">{{ $product['description'] ?? 'Premium quality product with excellent craftsmanship and attention to detail.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        @if(isset($relatedProducts) && count($relatedProducts) > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="mb-4">Related Products</h2>
                    <div class="title-divider1 mb-4"></div>
                    <div class="row g-4">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="vs-product product-style1 h-100">
                                    <div class="product-img-wrapper">
                                        <div class="product-badge">
                                            @if($relatedProduct['type'] === 'authorized')
                                                AUTHORIZED
                                            @elseif($relatedProduct['type'] === 'optional')
                                                OPTIONAL
                                            @elseif($relatedProduct['type'] === 'merchandised')
                                                MERCHANDISED
                                            @else
                                                BACK TO SCHOOL
                                            @endif
                                        </div>
                                        <div class="product-img">
                                            <a href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id']]) }}">
                                                @if(isset($relatedProduct['image']) && $relatedProduct['image'])
                                                    <img src="{{ $relatedProduct['image'] }}" alt="{{ $relatedProduct['name'] }}" class="w-100">
                                                @else
                                                    <img src="{{ asset('assets/img/product/product1-1.png') }}" alt="{{ $relatedProduct['name'] }}" class="w-100">
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <span class="product-price">₹{{ number_format($relatedProduct['price']) }}</span>
                                        <h3 class="product-title">
                                            <a class="text-inherit" href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id']]) }}">
                                                {{ $relatedProduct['name'] }}
                                            </a>
                                        </h3>
                                        <div class="actions">
                                            <a href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id']]) }}" class="vs-btn">
                                                <i class="far fa-shopping-cart"></i>Shop Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- FAQ Section -->
        <section class="space-extra-bottom mt-5">
            <div class="container">
                <div class="row gx-80">
                    <div class="col-lg-12 align-self-center">
                        <div class="title-area text-center text-lg-start">
                            <span class="sec-subtitle">Clear Your Doubts</span>
                            <h2 class="sec-title">Frequently Asked Questions</h2>
                        </div>
                        <div class="accordion accordion-style1 faq-two-column" id="faqVersion1">
                            <div class="accordion-item active">
                                <div class="accordion-header" id="headingOne1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne1" aria-expanded="true" aria-controls="collapseOne1">
                                        How can I contact customer support?
                                    </button>
                                </div>
                                <div id="collapseOne1" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne1" data-bs-parent="#faqVersion1">
                                    <div class="accordion-body">
                                        <p>You can reach us via email at hello@theskoolstore.com or call us at +91
                                            9994878486. Our support team is happy to assist you!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingTwo1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo1">
                                        What payment methods do you accept?
                                    </button>
                                </div>
                                <div id="collapseTwo1" class="accordion-collapse collapse" aria-labelledby="headingTwo1"
                                    data-bs-parent="#faqVersion1">
                                    <div class="accordion-body">
                                        <p>We accept online payments via credit/debit cards, UPI, net banking, and other
                                            secure payment options. </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingThree1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree1" aria-expanded="false"
                                        aria-controls="collapseThree1">
                                        How do I choose the correct size?
                                    </button>
                                </div>
                                <div id="collapseThree1" class="accordion-collapse collapse" aria-labelledby="headingThree1"
                                    data-bs-parent="#faqVersion1">
                                    <div class="accordion-body">
                                        <p>
                                            Each product page includes a size chart to help you select the perfect fit. If
                                            you're unsure, refer to the explanation video provided for each garment.

                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingFour1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFour1" aria-expanded="false" aria-controls="collapseFour1">
                                        What if my school is not listed on the website?
                                    </button>
                                </div>
                                <div id="collapseFour1" class="accordion-collapse collapse" aria-labelledby="headingFour1"
                                    data-bs-parent="#faqVersion1">
                                    <div class="accordion-body">
                                        <p>
                                            We currently have uniforms available only for listed schools. However, we are
                                            continuously expanding our collection, so stay tuned!
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingFive1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFive1" aria-expanded="false" aria-controls="collapseFive1">
                                        How do I know if my order was placed successfully?
                                    </button>
                                </div>
                                <div id="collapseFive1" class="accordion-collapse collapse" aria-labelledby="headingFive1"
                                    data-bs-parent="#faqVersion1">
                                    <div class="accordion-body">
                                        <p>Enrolment Events are like open days or open weeks at Busy Bees. It's a chance for
                                            you to visit your local nursery, take a look around, and see some of exciting
                                            activities in action. </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <div class="accordion-header" id="headingSix1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseSix1" aria-expanded="false" aria-controls="collapseSix1">
                                        How do I find my school's uniform?
                                    </button>
                                </div>
                                <div id="collapseSix1" class="accordion-collapse collapse" aria-labelledby="headingSix1"
                                    data-bs-parent="#faqVersion1">
                                    <div class="accordion-body">
                                        <p>Simply enter your school name in the search bar, and you'll see the authorized
                                            and optional products available.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

<!-- Size Guide Modal -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sizeGuideModalLabel">Size Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="size-guide-image">
                            <img src="{{ asset('assets/img/product/size_guide/15851743422781.jpg') }}" alt="Size Guide" class="w-100" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="size-guide-video">
                            <div class="ratio ratio-16x9">
                                <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Size Guide Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product-image-gallery {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .product-thumbnails-vertical {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100px;
        flex-shrink: 0;
    }

    .product-big-img {
        flex: 1;
    }

    .main-image-container {
        background-color: #f8f5ff;
        border-radius: 12px;
        padding: 20px;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .main-image-container img {
        max-width: 100%;
        max-height: 500px;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .product-actions-center-wrapper {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 12px;
        margin-top: 20px;
    }

    .product-actions-center {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }

    .title-divider1 {
        height: 3px;
        width: 80px;
        background-color: #490D59;
        margin-bottom: 20px;
    }

    /* Related Products Styles */
    .product-img-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 30px 30px 0 0;
        background-color: #f8f5ff;
    }

    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #000000;
        color: #ffffff;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        z-index: 2;
        border-radius: 4px;
    }

    .product-img {
        width: 100%;
        height: 280px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f5ff;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 15px;
    }

    .vs-product.product-style1 {
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 3px solid var(--theme-color2, #e0d5f0);
        border-radius: 30px;
        transition: all ease 0.4s;
        overflow: hidden;
    }

    .vs-product.product-style1:hover {
        border-color: var(--theme-color, #490D59);
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .product-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-price {
        font-size: 22px;
        font-weight: 500;
        color: #dc3545;
        font-family: var(--title-font, inherit);
        margin-bottom: 12px;
        display: block;
        line-height: 1;
    }

    .product-title {
        font-size: 16px;
        margin-bottom: 12px;
        text-transform: capitalize;
        line-height: 1.4;
        min-height: 44px;
    }

    .product-title a {
        color: #333;
        text-decoration: none;
    }

    .product-title a:hover {
        color: #490D59;
    }

    .actions {
        margin-top: auto;
        display: flex;
        justify-content: center;
    }

    .product-style1 .vs-btn {
        background-color: var(--vs-secondary-color, #e0d5f0);
        padding: 17px 26px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-style1 .vs-btn:after,
    .product-style1 .vs-btn:before {
        background-color: var(--theme-color, #490D59);
    }

    .product-style1 .vs-btn i {
        margin-right: 10px;
    }

    .thumb-item {
        width: 100px;
        height: 100px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumb-item:hover,
    .thumb-item.active {
        border-color: #490D59;
        transform: scale(1.05);
    }

    .size-option {
        position: relative;
        display: inline-block;
    }

    .size-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .size-option span {
        display: inline-block;
        padding: 10px 20px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #ffffff;
        min-width: 60px;
        text-align: center;
    }

    .size-option input[type="radio"]:checked + span {
        border-color: #490D59;
        background-color: #490D59;
        color: #ffffff;
    }

    .size-option:hover span {
        border-color: #490D59;
    }

    .form-select {
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 16px;
        font-weight: 600;
    }

    .form-select:focus {
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
        outline: none;
    }

    .product-about {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .product-title {
        font-size: 28px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .product-price {
        font-size: 32px;
        font-weight: 600;
        color: #dc3545;
        margin-bottom: 5px;
    }

    .product-text {
        color: #666;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .actions {
        display: flex;
        gap: 10px;
    }

    .vs-btn {
        flex: 1;
    }

    @media (max-width: 991px) {
        .product-image-gallery {
            flex-direction: column;
        }

        .product-thumbnails-vertical {
            flex-direction: row;
            width: 100%;
            overflow-x: auto;
            order: 2;
        }

        .product-big-img {
            order: 1;
        }

        .thumb-item {
            flex-shrink: 0;
        }

        .main-image-container {
            min-height: 400px;
        }

        .main-image-container img {
            max-height: 400px;
        }

        .product-actions-center {
            justify-content: center;
        }
    }
</style>

<script>
    function changeMainImage(imageSrc) {
        const mainImage = document.getElementById('mainProductImage');
        if (mainImage) {
            mainImage.src = imageSrc;
        }
        
        // Update active thumbnail
        document.querySelectorAll('.thumb-item').forEach(thumb => {
            thumb.classList.remove('active');
            if (thumb.getAttribute('data-image') === imageSrc) {
                thumb.classList.add('active');
            }
        });
    }


    // Enable Buy Now button when size is selected
    document.addEventListener('DOMContentLoaded', function() {
        const sizeInputs = document.querySelectorAll('input[name="size"]');
        const buyNowBtn = document.getElementById('buy-now-btn');
        const buyNowSize = document.getElementById('buy-now-size');
        const quantityInput = document.getElementById('quantity');
        const buyNowQuantity = document.getElementById('buy-now-quantity');
        const addToCartForm = document.getElementById('addToCartForm');

        // Sync quantity from dropdown
        if (quantityInput) {
            quantityInput.addEventListener('change', function() {
                buyNowQuantity.value = this.value;
            });
        }

        // Enable/disable Buy Now and update hidden fields based on size selection
        sizeInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.checked) {
                    const sizeValue = this.value;
                    buyNowSize.value = sizeValue;
                    document.getElementById('cart-size').value = sizeValue;
                    buyNowBtn.disabled = false;
                }
            });
        });

        // Update buy now size when add to cart form size changes
        addToCartForm.addEventListener('submit', function(e) {
            const selectedSize = document.querySelector('input[name="size"]:checked');
            if (!selectedSize) {
                e.preventDefault();
                alert('Please select a size');
                return false;
            }
            document.getElementById('cart-size').value = selectedSize.value;
        });

        // Sync quantity from dropdown to both forms
        if (quantityInput) {
            quantityInput.addEventListener('change', function() {
                const qtyValue = this.value;
                buyNowQuantity.value = qtyValue;
                document.getElementById('cart-quantity').value = qtyValue;
            });
        }
    });
</script>
@endsection
