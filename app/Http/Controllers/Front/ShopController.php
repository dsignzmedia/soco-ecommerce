<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductMapping;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProductMapping::whereIn('product_type', ['merchandised', 'back_to_school'])
            ->where('status', 'live');

        if ($request->has('category')) {
             $query->where('category', $request->category);
        }

        $products = $query->paginate(12);
        
        // Get categories for sidebar
        $categories = ProductMapping::whereIn('product_type', ['merchandised', 'back_to_school'])
            ->where('status', 'live')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('frontend.shop.index', compact('products', 'categories'));
    }

    public function show($id): View
    {
        $dbProduct = ProductMapping::with('variants')->findOrFail($id);

        // Determine image URL
        $image = $dbProduct->featured_image
            ? (Str::startsWith($dbProduct->featured_image, 'http') ? $dbProduct->featured_image : asset('storage/' . $dbProduct->featured_image))
            : asset('assets/img/product/product1-1.png');

        // Handle media images if available
        $images = [$image];
        $gallerySource = $dbProduct->media_gallery ?? $dbProduct->media_images;
        
        if ($gallerySource && is_array($gallerySource)) {
            foreach ($gallerySource as $mediaImg) {
                // Handle potential double-nesting or bad data structure
                if (is_array($mediaImg)) {
                    $mediaImg = $mediaImg[0] ?? null;
                }
                if (is_string($mediaImg) && !empty($mediaImg)) {
                    $images[] = Str::startsWith($mediaImg, 'http') ? $mediaImg : asset('storage/' . $mediaImg);
                }
            }
        }

        // Determine sizes from variants or fallback
        $sizes = ['Standard'];
        if ($dbProduct->variants && $dbProduct->variants->count() > 0) {
            $sizes = $dbProduct->variants->pluck('option')->toArray();
        }

        $product = [
            'id' => $dbProduct->id,
            'name' => $dbProduct->product_name,
            'price' => $dbProduct->price_regular,
            'original_price' => $dbProduct->price_sale,
            'image' => $image,
            'images' => $images,
            'description' => $dbProduct->description,
            'type' => $dbProduct->product_type ?? 'merchandised',
            'category' => $dbProduct->category ?? 'General',
            'sizes' => $sizes,
            'size_chart_path' => $dbProduct->size_chart_path,
            'size_measurement_image' => $dbProduct->size_measurement_image,
            'video_url' => $dbProduct->video_url,
            'tags' => $dbProduct->tag_name ? explode(',', $dbProduct->tag_name) : [],
            'sku' => $dbProduct->id,
            'variants' => $dbProduct->variants,
        ];

        // Fetch related products (same category)
        // Fetch related products (same category, fallback to random)
        // Fetch related products (same category, fallback to random)
        $relatedProductsQuery = ProductMapping::where('id', '!=', $id)
            ->where('status', 'live')
            ->where('product_type', $dbProduct->product_type);
            
        if (!empty($dbProduct->category)) {
            $relatedProductsQuery->where('category', $dbProduct->category);
        }
        
        $relatedProductsModels = $relatedProductsQuery->inRandomOrder()->take(4)->get();
        
        // Fallback if not enough products found
        if ($relatedProductsModels->count() < 4) {
            $fetchedIds = $relatedProductsModels->pluck('id')->toArray();
            $fetchedIds[] = $id; 
            
            $limit = 4 - $relatedProductsModels->count();
            
            $otherProducts = ProductMapping::whereNotIn('id', $fetchedIds)
                ->where('status', 'live')
                ->where('product_type', $dbProduct->product_type)
                ->inRandomOrder()
                ->take($limit)
                ->get();
                
            $relatedProductsModels = $relatedProductsModels->merge($otherProducts);
        }

        $relatedProducts = $relatedProductsModels->map(function($p) {
             return [
                'id' => $p->id,
                'name' => $p->product_name,
                // If sale price is lower than regular price, use it as main price
                'price' => ($p->price_sale > 0 && $p->price_sale < $p->price_regular) ? $p->price_sale : $p->price_regular,
                'original_price' => $p->price_regular,
                'image' => $p->featured_image ? (Str::startsWith($p->featured_image, 'http') ? $p->featured_image : asset('storage/' . $p->featured_image)) : asset('assets/img/product/product1-1.png'),
             ];
        });

        return view('frontend.shop.detail', compact('product', 'relatedProducts'));
    }

    public function addToCart(Request $request)
    {
        if (!auth()->check()) {
             return redirect()->route('login')->with('info', 'Please login or register to continue shopping.');
        }

        $request->validate([
            'product_id' => 'required|integer',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

         // Check Stock (Reusing logic from AuthController)
        $product = ProductMapping::with('variants')->find($request->product_id);
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        if ($product->variants->count() > 0) {
            $variant = $product->variants->where('option', $request->size)->first();
            if (!$variant) {
                 return back()->with('error', 'Invalid size selected.');
            }
             if ($variant->stock < $request->quantity) {
                return back()->with('error', 'Selected size is out of stock.');
            }
         } else {
             if ($product->inventory_stock < $request->quantity) {
                 return back()->with('error', 'Product is out of stock.');
             }
         }

        // Add to Cart Logic
         // Note: profile_id is NULL for Guest shopping
        $existingItem = \App\Models\Cart::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('size', $request->size)
            ->whereNull('profile_id') // IMPORTANT: Look for general cart item
            ->first();

        if ($existingItem) {
             // Update quantity
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
        } else {
             // Create new item
             \App\Models\Cart::create([
                 'user_id' => $user->id,
                 'profile_id' => null, // Explicitly null
                 'product_id' => $product->id,
                 'size' => $request->size,
                 'quantity' => $request->quantity,
             ]);
        }

        // Redirect to Cart page
        return redirect()->route('frontend.parent.cart')->with('success', 'Product added to cart successfully!');
    }
}
