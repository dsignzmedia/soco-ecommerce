<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = Coupon::latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coupons.form', [
            'coupon' => new Coupon(),
            'mode' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Auto-generates a 48-hour free shipping coupon.
     * Format: SOCO + 2 random numbers + 2 random letters (e.g., SOCO12AC)
     * Only allows generation if no active coupon exists.
     */
    public function store(Request $request)
    {
        // Check if there's already an active coupon
        $activeCoupon = Coupon::where('is_active', true)
            ->where('valid_to', '>', now())
            ->first();
            
        if ($activeCoupon) {
            // If AJAX request, return JSON for modal
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An active coupon already exists',
                    'coupon' => [
                        'id' => $activeCoupon->id,
                        'code' => $activeCoupon->code,
                        'expires_at' => $activeCoupon->valid_to->format('M d, Y \a\t h:i A'),
                        'expires_in' => $activeCoupon->valid_to->diffForHumans(),
                    ]
                ], 422);
            }
            
            return redirect()->route('master.admin.coupons.index')
                ->with('error', 'Cannot generate new coupon. An active coupon already exists: ' . $activeCoupon->code . ' (expires at ' . $activeCoupon->valid_to->format('Y-m-d H:i:s') . ')');
        }
        
        // Auto-generate code: SOCO + 2 random numbers + 2 random letters
        // Example: SOCO12AC, SOCO45XY, SOCO78QW
        $numbers = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT); // 2 random numbers (00-99)
        $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2)); // 2 random letters
        $code = 'SOCO' . $numbers . $letters;
        
        // Ensure uniqueness (very unlikely to collide, but just in case)
        while(Coupon::where('code', $code)->exists()) {
            $numbers = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
            $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2));
            $code = 'SOCO' . $numbers . $letters;
        }

        $newCoupon = Coupon::create([
            'code' => $code,
            'type' => 'free_shipping',
            'discount_amount' => 0,
            'discount_percentage' => 0,
            'valid_from' => now(),
            'valid_to' => now()->addHours(48),
            'usage_limit' => null, // Unlimited usage within time window
            'is_active' => true,
            'description' => 'Auto-generated 48-hour Free Shipping Coupon',
        ]);

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon generated successfully!',
                'coupon' => [
                    'code' => $code,
                    'valid_until' => now()->addHours(48)->format('M d, Y \a\t h:i A'),
                ]
            ]);
        }

        return redirect()->route('master.admin.coupons.index')
            ->with('status', 'New 48-Hour Free Shipping Coupon generated successfully: ' . $code . ' (Valid until ' . now()->addHours(48)->format('Y-m-d H:i:s') . ')');
    }

    /**
     * Display the specified resource.
     * Shows orders associated with this coupon.
     */
    public function show(Coupon $coupon)
    {
        $orders = $coupon->orders()->latest()->paginate(20);
        return view('admin.coupons.show', compact('coupon', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', [
            'coupon' => $coupon,
            'mode' => 'edit'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        // Handle AJAX deactivation request
        if ($request->ajax() || $request->wantsJson()) {
            if ($request->has('is_active')) {
                $coupon->update(['is_active' => $request->is_active]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Coupon deactivated successfully'
                ]);
            }
        }
        
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percentage,free_shipping',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'description' => 'nullable|string'
        ]);
        
        // Handle checkbox unchecked state
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('master.admin.coupons.index')
            ->with('status', 'Coupon updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('master.admin.coupons.index')
            ->with('status', 'Coupon deleted successfully.');
    }
}
