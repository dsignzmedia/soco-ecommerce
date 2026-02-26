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
        // Default allowed types
        $allowedTypes = ['merchandised', 'back_to_school'];

        // Filter by product type if provided
        if ($request->has('product_type')) {
            $typesInput = is_array($request->product_type) ? $request->product_type : [$request->product_type];
            $validTypes = array_intersect($typesInput, $allowedTypes);
            
            if (!empty($validTypes)) {
                $allowedTypes = array_values($validTypes);
            }
        }

        // Fetch all products matching types, regardless of school_id (Global + School Specific)
        $query = ProductMapping::whereIn('product_type', $allowedTypes)
            ->where('status', 'live');

        if ($request->has('category')) {
             $categoriesFilter = is_array($request->category) ? $request->category : [$request->category];
             $query->whereIn('category', $categoriesFilter);
        }

        // Get categories that have at least one product matching the filtered product types
        $categories = ProductMapping::whereIn('product_type', $allowedTypes)
            ->where('status', 'live')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        
        $products = $query->paginate(48);

        return view('frontend.shop.index', compact('products', 'categories'));
    }

    public function show($id): View
    {
        $dbProduct = ProductMapping::with('variants')
            ->where('status', 'live')
            ->findOrFail($id);

        // Determine image URL
        $image = $dbProduct->featured_image
            ? (Str::startsWith($dbProduct->featured_image, 'http') ? $dbProduct->featured_image : asset('storage/' . $dbProduct->featured_image))
            : asset('assets/img/product/product1-1.png');

        // Handle media images if available
        $images = [$image];
        
        // Check media_gallery first (primary field), then fallback to media_images
        $gallerySource = null;
        if (!empty($dbProduct->media_gallery) && is_array($dbProduct->media_gallery) && count($dbProduct->media_gallery) > 0) {
            $gallerySource = $dbProduct->media_gallery;
            \Log::info('[BTS Product Detail] Using media_gallery', [
                'product_id' => $dbProduct->id,
                'gallery_count' => count($gallerySource)
            ]);
        } elseif (!empty($dbProduct->media_images) && is_array($dbProduct->media_images) && count($dbProduct->media_images) > 0) {
            $gallerySource = $dbProduct->media_images;
            \Log::info('[BTS Product Detail] Using media_images (fallback)', [
                'product_id' => $dbProduct->id,
                'gallery_count' => count($gallerySource)
            ]);
        } else {
            \Log::warning('[BTS Product Detail] No gallery images found', [
                'product_id' => $dbProduct->id,
                'has_media_gallery' => !empty($dbProduct->media_gallery),
                'has_media_images' => !empty($dbProduct->media_images),
                'media_gallery_type' => gettype($dbProduct->media_gallery),
                'media_images_type' => gettype($dbProduct->media_images)
            ]);
        }
        
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
        
        \Log::info('[BTS Product Detail] Final images array', [
            'product_id' => $dbProduct->id,
            'total_images' => count($images),
            'images' => $images
        ]);

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
            'video_file' => $dbProduct->video_file,
            'delivery_duration' => $dbProduct->delivery_duration,
            'tags' => $dbProduct->tag_name ? explode(',', $dbProduct->tag_name) : [],
            'sku' => $dbProduct->sku ?? $dbProduct->id,
            'gender' => $dbProduct->gender,
            'grade' => $dbProduct->grade,
            'variants' => $dbProduct->variants,
        ];

        // Fetch related products (same category, same school, same grade)
        $relatedProductsQuery = ProductMapping::where('id', '!=', $id)
            ->where('status', 'live')
            ->where('product_type', $dbProduct->product_type);
            
        if (!empty($dbProduct->category)) {
            $relatedProductsQuery->where('category', $dbProduct->category);
        }

        // Strict filtering for School Products
        if ($dbProduct->school_id) {
            $relatedProductsQuery->where('school_id', $dbProduct->school_id);
        } else {
            // For general products (Merch/BTS without school), only show other general products
            $relatedProductsQuery->whereNull('school_id');
        }

        // Grade-wise filtering: only show products for the same grade
        if (!empty($dbProduct->grade)) {
            $relatedProductsQuery->where('grade', $dbProduct->grade);
        }

        // Gender filtering: only show products for the same gender
        if (!empty($dbProduct->gender)) {
            $relatedProductsQuery->where('gender', $dbProduct->gender);
        }
        
        $relatedProductsModels = $relatedProductsQuery->inRandomOrder()->take(4)->get();
        
        // Fallback if not enough products found — broaden to same school+grade but any category
        if ($relatedProductsModels->count() < 4) {
            $fetchedIds = $relatedProductsModels->pluck('id')->toArray();
            $fetchedIds[] = $id; 
            
            $limit = 4 - $relatedProductsModels->count();
            
            $fallbackQuery = ProductMapping::whereNotIn('id', $fetchedIds)
                ->where('status', 'live')
                ->where('product_type', $dbProduct->product_type);

            // Keep same school constraint
            if ($dbProduct->school_id) {
                $fallbackQuery->where('school_id', $dbProduct->school_id);
            } else {
                $fallbackQuery->whereNull('school_id');
            }

            // Keep same grade constraint in fallback too
            if (!empty($dbProduct->grade)) {
                $fallbackQuery->where('grade', $dbProduct->grade);
            }

            // Keep same gender constraint in fallback too
            if (!empty($dbProduct->gender)) {
                $fallbackQuery->where('gender', $dbProduct->gender);
            }
                
            $otherProducts = $fallbackQuery->inRandomOrder()->take($limit)->get();
                
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
                'product_tag' => $p->product_tag,
                'show_product_tag' => $p->show_product_tag,
                'gender' => $p->gender,
                'grade' => $p->grade,
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
