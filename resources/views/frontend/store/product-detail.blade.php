@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper " data-bg-src="{{ asset('assets/img/contact/Background.png') }}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">{{ $product['name'] ?? 'Product Details' }}</h1>
            <p class="breadcumb-text">{{ Str::limit($product['description'] ?? 'Explore Product Details, Reviews, And Specifications', 80) }}</p>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><a href="{{ route('frontend.parent.dashboard', ['student_id' => $selectedProfile['id'] ?? '']) }}">Parent Dashboard</a></li>
                    <li><a href="{{ route('frontend.parent.store', ['profile_id' => $selectedProfile['id'] ?? '']) }}">Store</a></li>
                    <li>{{ Str::limit($product['name'] ?? 'Product Details', 30) }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="vs-product-wrapper product-details space-top space-extra-bottom" style="background-color: #ffffff;">
    <div class="container">
        <div class="row gx-60">
            <!-- Left: Product Images -->
            <div class="col-lg-6">
                <div class="product-big-img vs-carousel" data-slide-show="1" data-fade="true" data-asnavfor=".product-thumb-slide">
                        @php
                            $productImages = $product['images'] ?? [$product['image'] ?? asset('assets/img/product/product1-1.png')];
                        @endphp
                        @foreach($productImages as $index => $image)
                        <div class="img">
                                <img src="{{ $image }}" alt="{{ $product['name'] }} - Image {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                <div class="product-thumb-slide row vs-carousel" data-slide-show="4" data-md-slide-show="4" data-sm-slide-show="3" data-xs-slide-show="3" data-asnavfor=".product-big-img">
                    @foreach($productImages as $index => $image)
                        <div class="col-3">
                            <div class="thumb">
                                <img src="{{ $image }}" alt="{{ $product['name'] }} - Image {{ $index + 1 }}">
                        </div>
                            </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Product Information -->
            <div class="col-lg-6 align-self-center">
                <div class="product-about">
                    <p class="product-price">
                        ₹{{ number_format($product['price']) }}
                        @if(isset($product['original_price']) && $product['original_price'] > $product['price'])
                            <del>₹{{ number_format($product['original_price']) }}</del>
                        @endif
                    </p>
                    
                    <h2 class="product-title">{{ $product['name'] }}</h2>
                    
                    <div class="product-rating">
                        <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5">
                            <span style="width:100%">Rated <strong class="rating">5.00</strong> out of 5 based on <span class="rating">5</span> customer rating</span>
                        </div>
                        <span>(13)</span>
                    </div>

                    <p class="product-text">{{ $product['description'] ?? 'Premium quality product with excellent craftsmanship and attention to detail. We think your skin should look and refreshed matter Nourish your outer inner beauty with our essential oil infused beauty products.' }}</p>

                    @php
                        $defaultSize = isset($product['sizes']) && count($product['sizes']) > 0 ? $product['sizes'][0] : 'Standard';
                    @endphp
                    <form action="{{ route('frontend.parent.add-to-cart') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="profile_id" value="{{ $selectedProfile['id'] }}">
                        <input type="hidden" name="size" id="cart-size" value="{{ $defaultSize }}" required>
                        <input type="hidden" name="quantity" id="cart-quantity" value="1" required>
                        
                        <!-- Size Selection -->
                        @if(isset($product['sizes']) && count($product['sizes']) > 0)
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <label class="form-label fw-bold mb-0">Size:</label>
                                <a href="#" class="text-primary small" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" style="text-decoration: underline;">Size Guide</a>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($product['sizes'] as $size)
                                    <label class="size-option">
                                        <input type="radio" name="size" value="{{ $size }}" {{ $loop->first ? 'checked' : '' }} required>
                                        <span>{{ $size }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="actions">
                            <div class="quantity">
                                <label for="quantity" class="screen-reader-text">Quantity:</label>
                                <button type="button" class="quantity-minus qty-btn"><i class="fal fa-minus"></i></button>
                                <input type="number" id="quantity" class="qty-input" step="1" min="1" max="100" name="quantity" value="1" title="Qty">
                                <button type="button" class="quantity-plus qty-btn"><i class="fal fa-plus"></i></button>
                        </div>
                            <button type="submit" class="vs-btn">Add to Cart</button>
                            <a href="#" class="icon-btn"><i class="far fa-heart"></i></a>
                        </div>
                    </form>

                    <div class="product-getway">
                        <span class="getway-title">GUARANTEED SAFE CHECKOUT:</span>
                        <img src="{{ asset('assets/img/widget/cards-2.png') }}" alt="cards">
                    </div>

                    <div class="product_meta">
                        @if(isset($product['sku']))
                            <span class="sku_wrapper">SKU: <span class="sku">#{{ $product['sku'] }}</span></span>
                        @endif
                        @if(isset($product['category']))
                            <span class="posted_in"><span class="category-label">Category:</span> <span class="category-value">{{ str_replace('_', ' ', $product['category']) }}</span></span>
                        @endif
                        @if(isset($product['tags']) && count($product['tags']) > 0)
                            <span>Tags: 
                                @foreach($product['tags'] as $tag)
                                    <a href="#" rel="tag">{{ $tag }}</a>
                                @endforeach
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
            <div class="row mt-5">
                <div class="col-12">
                <h2>Reviews</h2>
                <div class="title-divider1"></div>
                <div class="woocommerce-Reviews">
                    <div class="vs-comments-wrap">
                        <ul class="comment-list">
                            <li class="review vs-comment-item">
                                <div class="vs-post-comment">
                                    <div class="comment-avater">
                                        <img src="{{ asset('assets/img/author/Author1.png') }}" alt="Comment Author">
                                        </div>
                                    <div class="comment-content">
                                        <h4 class="name h4">Mark Jack</h4>
                                        <span class="commented-on"><i class="fal fa-calendar-alt"></i>22 April, 2023</span>
                                        <div class="review-rating">
                                            <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5">
                                                <span style="width: 100%">Rated <strong class="rating">5.00</strong> out of 5 based on <span class="rating">1</span> customer rating</span>
                                        </div>
                                    </div>
                                        <p class="text">Lorem ipsum dolor sit amet,  elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</p>
                                        </div>
                                    </div>
                            </li>
                            <li class="review vs-comment-item">
                                <div class="vs-post-comment">
                                    <div class="comment-avater">
                                        <img src="{{ asset('assets/img/author/Author2.png') }}" alt="Comment Author">
                                </div>
                                    <div class="comment-content">
                                        <h4 class="name h4">John Deo</h4>
                                        <span class="commented-on"><i class="fal fa-calendar-alt"></i>26 April, 2023</span>
                                        <div class="review-rating">
                                            <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5">
                                                <span style="width:80%">Rated <strong class="rating">5.00</strong> out of 5 based on <span class="rating">1</span> customer rating</span>
                            </div>
                    </div>
                                        <p class="text">The purpose of lorem ipsum is to create a natural looking block of text  that doesn't distract from the layout. A practice not without controversy, laying out pages with meaningless filler text can be very useful when the focus is meant to be on design, not content.</p>
                </div>
            </div>
                            </li>
                            <li class="review vs-comment-item">
                                <div class="vs-post-comment">
                                    <div class="comment-avater">
                                        <img src="{{ asset('assets/img/author/Comment Author.png') }}" alt="Comment Author">
                        </div>
                                    <div class="comment-content">
                                        <h4 class="name h4">Tara sing</h4>
                                        <span class="commented-on"><i class="fal fa-calendar-alt"></i>26 April, 2023</span>
                                        <div class="review-rating">
                                            <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5">
                                                <span style="width:100%">Rated <strong class="rating">4.00</strong> out of 5 based on <span class="rating">1</span> customer rating</span>
                                </div>
                                    </div>
                                        <p class="text">The passage experienced a surge in 1960s when Letraset used it on their dry-transfer sheets, and again during the 90s as desktop publishers bundled the text with their software. Today it's seen all around the web; on templates, websites, and stock designs. Use our generator</p>
                                </div>
                            </div>
                            </li>
                        </ul>
                                </div>
                                    </div>

                <!-- Comment Form -->
                <div class="vs-comment-form review-form">
                    <div id="respond" class="comment-respond">
                        <div class="form-title">
                            <h3 class="blog-inner-title">Post Review</h3>
                        </div>
                        <div class="row">
                            <div class="form-group rating-select">
                                <label>Your Rating</label>
                                <p class="stars">
                                    <span>
                                        <a class="star-1" href="#">1</a>
                                        <a class="star-2" href="#">2</a>
                                        <a class="star-3" href="#">3</a>
                                        <a class="star-4" href="#">4</a>
                                        <a class="star-5" href="#">5</a>
                                    </span>
                                        </p>
                                    </div>
                            <div class="col-md-6 form-group">
                                <input type="text" class="form-control" placeholder="Complete Name">
                                </div>
                            <div class="col-md-6 form-group">
                                <input type="email" class="form-control" placeholder="Email Address">
                            </div>
                            <div class="col-12 form-group">
                                <textarea class="form-control" placeholder="Review"></textarea>
                                </div>
                            <div class="col-12 form-group mb-0">
                                <button class="vs-btn">Post Review</button>
                                    </div>
                                </div>
                            </div>
                                </div>
                                    </div>
                                </div>

        <!-- Related Products Section -->
        @php
            $relatedProductsList = $relatedProducts ?? ($allProducts ?? []);
            // If no products available, create sample products for display
            if(count($relatedProductsList) == 0) {
                $productImages = [
                    asset('assets/img/product_images/Image1.png'),
                    asset('assets/img/product_images/Image2.png'),
                    asset('assets/img/product_images/Image3.png'),
                    asset('assets/img/product_images/Image4.png'),
                    asset('assets/img/product_images/Image5.png'),
                    asset('assets/img/product_images/Image6.png'),
                    asset('assets/img/product_images/Image7.png'),
                    asset('assets/img/product_images/Image8.png'),
                ];
                $relatedProductsList = [
                    ['id' => 1, 'name' => 'The Bubblegum Toy', 'price' => 560, 'original_price' => 700, 'image' => $productImages[0]],
                    ['id' => 2, 'name' => 'Table Harmoni Play', 'price' => 480, 'original_price' => null, 'image' => $productImages[1]],
                    ['id' => 3, 'name' => 'Tommy Speak Head', 'price' => 620, 'original_price' => 750, 'image' => $productImages[2]],
                    ['id' => 4, 'name' => 'Queen Radio Home', 'price' => 450, 'original_price' => null, 'image' => $productImages[3]],
                ];
            } else {
                // Update existing related products to use product_images if they have old image paths
                foreach($relatedProductsList as &$product) {
                    if(isset($product['image']) && strpos($product['image'], 'product1-') !== false) {
                        // Replace old product images with random product_images
                        $productImages = [
                            asset('assets/img/product_images/Image1.png'),
                            asset('assets/img/product_images/Image2.png'),
                            asset('assets/img/product_images/Image3.png'),
                            asset('assets/img/product_images/Image4.png'),
                            asset('assets/img/product_images/Image5.png'),
                            asset('assets/img/product_images/Image6.png'),
                            asset('assets/img/product_images/Image7.png'),
                            asset('assets/img/product_images/Image8.png'),
                        ];
                        $product['image'] = $productImages[array_rand($productImages)];
                    }
                }
                unset($product);
            }
            $relatedProductsList = array_slice($relatedProductsList, 0, 4); // Show max 4 products
        @endphp
        <div class="row mt-5">
            <div class="col-12">
                <h2>Related Products</h2>
                <div class="title-divider1"></div>
                <div class="row vs-carousel" data-slide-show="4" data-lg-slide-show="3" data-md-slide-show="2">
                    @foreach($relatedProductsList as $relatedProduct)
                        <div class="col-md-6 col-lg-3 col-xl-3">
                            <div class="vs-product product-style1">
                                <div class="product-img">
                                    <a href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}">
                                        @if(isset($relatedProduct['image']) && $relatedProduct['image'])
                                            <img src="{{ $relatedProduct['image'] }}" alt="{{ $relatedProduct['name'] }}" class="w-100">
                                        @else
                                            <img src="{{ asset('assets/img/product_images/Image1.png') }}" alt="{{ $relatedProduct['name'] }}" class="w-100">
                                        @endif
                                    </a>
                            </div>
                                <div class="product-content">
                                    <span class="product-price">
                                        ₹{{ number_format($relatedProduct['price'] ?? 0) }}
                                        @if(isset($relatedProduct['original_price']) && $relatedProduct['original_price'] > ($relatedProduct['price'] ?? 0))
                                            <del>₹{{ number_format($relatedProduct['original_price']) }}</del>
                                        @endif
                                    </span>
                                    <h3 class="product-title">
                                        <a class="text-inherit" href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}">
                                            {{ $relatedProduct['name'] }}
                                        </a>
                                    </h3>
                                    <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5">
                                        <span style="width:100%">Rated <strong class="rating">5.00</strong> out of 5</span>
                                </div>
                                    <div class="actions">
                                        <a href="{{ route('frontend.parent.product-detail', ['productId' => $relatedProduct['id'], 'profile_id' => $selectedProfile['id'] ?? '']) }}" class="vs-btn">
                                            <i class="far fa-shopping-cart"></i>Add to Cart
                                        </a>
                                        <a href="#" class="icon-btn"><i class="far fa-heart"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
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
    /* Product Image Carousel */
    .product-big-img {
        margin-bottom: 15px;
    }

    .product-big-img .img {
        width: 100%;
        height: 500px;
        background-color: #f8f5ff;
        border-radius: 12px;
        overflow: hidden;
        padding: 0;
        margin: 0;
        border: 2px solid #e0d5f0;
    }

    .product-big-img .img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 12px;
    }

    .product-thumb-slide {
        margin: 0 -5px;
    }

    .product-thumb-slide .col-3 {
        padding: 0 5px;
    }

    .product-thumb-slide .thumb {
        width: 100%;
        height: 100px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f5ff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-thumb-slide .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-thumb-slide .thumb:hover,
    .product-thumb-slide .thumb.active {
        border-color: #490D59;
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
        background-color: #ffffff;
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
        background-color: #ffffff;
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
        transition: border-color ease 0.4s, box-shadow ease 0.4s;
        overflow: hidden;
        position: relative;
    }

    .vs-product.product-style1:hover {
        border-color: var(--theme-color, #490D59);
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
        align-items: center;
        gap: 10px;
    }

    .product-style1 .vs-btn {
        flex: 1;
    }

    .product-style1 .vs-btn i {
        margin-right: 8px;
        font-size: 14px;
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
        box-shadow: none;
        border: none;
        outline: none;
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
        margin-bottom: 15px;
    }

    .product-price del {
        font-size: 24px;
        color: #999;
        margin-left: 10px;
        font-weight: 400;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .product-rating .star-rating {
        margin-bottom: 0;
        font-size: 14px;
        line-height: 1.2;
        position: relative;
        display: inline-block;
        overflow: hidden;
    }

    .product-rating .star-rating span {
        display: block;
        position: relative;
        height: 1em;
        line-height: 1;
        font-size: 1em;
        width: 5.4em;
        font-family: star;
        color: #ffb900;
    }

    .product-rating .star-rating span:before {
        content: "\f005\f005\f005\f005\f005";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: #ffb900;
        font-size: 14px;
        letter-spacing: 2px;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .product-rating .star-rating span strong,
    .product-rating .star-rating span {
        font-size: 0;
        line-height: 0;
        color: transparent;
        overflow: hidden;
    }

    .product-rating .star-rating .rating {
        display: none;
    }

    .product-rating > span {
        color: #666;
        font-size: 14px;
        display: inline-block;
        margin-left: 5px;
    }

    .product-text {
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    /* Quantity Buttons */
    .quantity {
        display: flex;
        align-items: center;
        border: 1px solid #ffffff;
        border-radius: 8px;
        overflow: hidden;
        background-color: #ffffff;
    }

    .qty-btn {
        width: 40px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #333;
    }

    .qty-btn:hover {
        background-color: #490D59;
        color: #ffffff;
    }

    .qty-input {
        width: 60px;
        height: 50px;
        border: none;
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        padding: 0;
        background-color: #ffffff;
    }

    .qty-input:focus {
        outline: none;
        border: none;
        box-shadow: none;
    }
    
    .quantity:focus,
    .quantity:focus-within {
        outline: none;
        border: 1px solid #ffffff;
        box-shadow: none;
    }
    
    .product-about:focus,
    .product-about:focus-within {
        outline: none;
        border: none;
        box-shadow: none;
    }

    .screen-reader-text {
        position: absolute;
        clip: rect(1px, 1px, 1px, 1px);
        width: 1px;
        height: 1px;
        overflow: hidden;
    }

    .actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 30px;
    }

    /* Product Getway */
    .product-getway {
        margin-bottom: 25px;
        padding-top: 25px;
    }

    .getway-title {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-getway img {
        max-width: 100%;
        height: auto;
    }

    /* Product Meta */
    .product_meta {
        display: flex;
            flex-direction: column;
        gap: 10px;
        font-size: 14px;
        color: #666;
        padding-top: 20px;
    }

    .product_meta span {
        display: block;
    }

    .product_meta .category-label {
        color: #dc3545;
    }

    .product_meta .category-value {
        color: #000000;
    }

    .product_meta a {
        color: #490D59;
        text-decoration: none;
        margin-left: 5px;
    }

    .product_meta a:hover {
        text-decoration: underline;
    }

    .sku {
        font-weight: 600;
        color: #333;
    }

    @media (max-width: 991px) {
        .product-big-img .img {
            height: 400px;
            padding: 0;
        }

        .product-big-img .img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .actions {
            flex-wrap: wrap;
        }

        .actions .vs-btn {
            width: 100%;
        }
    }

    /* Reviews Section Styles */
    .woocommerce-Reviews {
        margin-bottom: 40px;
    }

    .vs-comments-wrap {
        margin-bottom: 40px;
    }

    .comment-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .vs-comment-item {
        margin-bottom: 30px;
        padding-bottom: 0;
    }

    .vs-comment-item:last-child {
        margin-bottom: 0;
    }

    .vs-post-comment {
        display: flex;
        gap: 20px;
    }

    .comment-avater {
            flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
    }

    .comment-avater img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .comment-content {
        flex: 1;
    }

    .comment-content .name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .commented-on {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #999;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .commented-on i {
        font-size: 14px;
    }

    .review-rating {
        margin-bottom: 15px;
    }

    .review-rating .star-rating {
        margin-bottom: 0;
        font-size: 14px;
        line-height: 1.2;
        position: relative;
        display: inline-block;
        overflow: hidden;
    }

    .review-rating .star-rating span {
        display: block;
        position: relative;
        height: 1em;
        line-height: 1;
        font-size: 1em;
        width: 5.4em;
        font-family: star;
        color: #ffb900;
    }

    .review-rating .star-rating span:before {
        content: "\f005\f005\f005\f005\f005";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: #ffb900;
        font-size: 14px;
        letter-spacing: 2px;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .review-rating .star-rating span strong,
    .review-rating .star-rating span {
        font-size: 0;
        line-height: 0;
        color: transparent;
        overflow: hidden;
    }

    .review-rating .star-rating .rating {
        display: none;
    }

    .comment-content .text {
        color: #666;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Review Form Styles */
    .vs-comment-form {
        margin-top: 40px;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .form-title {
        margin-bottom: 25px;
    }

    .blog-inner-title {
        font-size: 24px;
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }

    .rating-select {
        margin-bottom: 20px;
    }

    .rating-select label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .stars {
        margin: 0;
    }

    .stars span {
        display: flex;
        gap: 5px;
    }

    .stars a {
        color: #ddd;
        font-size: 20px;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .stars a:hover,
    .stars a.active {
        color: #ffb900;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0d5f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #490D59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }

    .form-control::placeholder {
        color: #999;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    @media (max-width: 767px) {
        .vs-post-comment {
            flex-direction: column;
        }

        .comment-avater {
            width: 60px;
            height: 60px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sizeInputs = document.querySelectorAll('input[name="size"]');
        const cartSizeInput = document.getElementById('cart-size');
        const quantityInput = document.getElementById('quantity');
        const quantityMinus = document.querySelector('.quantity-minus');
        const quantityPlus = document.querySelector('.quantity-plus');
        const addToCartForm = document.getElementById('addToCartForm');
        const cartQuantity = document.getElementById('cart-quantity');

        // Quantity +/- buttons
        if (quantityMinus && quantityInput) {
            quantityMinus.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value) || 1;
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                    if (cartQuantity) cartQuantity.value = quantityInput.value;
                }
            });
        }

        if (quantityPlus && quantityInput) {
            quantityPlus.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value) || 1;
                const maxValue = parseInt(quantityInput.getAttribute('max')) || 100;
                if (currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                    if (cartQuantity) cartQuantity.value = quantityInput.value;
                }
            });
        }

        // Update cart quantity when input changes
        if (quantityInput && cartQuantity) {
            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value) || 1;
                const min = parseInt(this.getAttribute('min')) || 1;
                const max = parseInt(this.getAttribute('max')) || 100;
                
                if (value < min) value = min;
                if (value > max) value = max;
                
                this.value = value;
                cartQuantity.value = value;
            });
        }

        // Update cart size when size is selected
        if (sizeInputs.length > 0) {
            if (cartSizeInput && !cartSizeInput.value) {
                cartSizeInput.value = sizeInputs[0].value;
            }
        sizeInputs.forEach(input => {
            input.addEventListener('change', function() {
                    if (this.checked && cartSizeInput) {
                        cartSizeInput.value = this.value;
                }
            });
        });
        } else if (cartSizeInput && !cartSizeInput.value) {
            cartSizeInput.value = 'Standard';
        }

        // Form submission validation
        if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            const selectedSize = document.querySelector('input[name="size"]:checked');
                if (sizeInputs.length > 0 && !selectedSize) {
                e.preventDefault();
                alert('Please select a size');
                return false;
            }
                if (cartQuantity) {
                    cartQuantity.value = quantityInput ? quantityInput.value : 1;
                }
            });
        }
    });
</script>
@endsection
