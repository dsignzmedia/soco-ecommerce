# Partial Return/Exchange Implementation Plan

## Current System Analysis

### Database Structure
1. **Orders Table:**
   - Each order item = 1 row in `orders` table
   - `quantity` field stores the quantity (can be > 1)
   - Example: User orders 2x Product A → 1 order row with `quantity = 2`

2. **Return/Exchange Requests Table:**
   - `return_exchange_requests` table links to `order_id`
   - Currently: 1 request = 1 order (entire quantity)
   - No field to track partial quantities

3. **Current Flow:**
   - User selects order items to return/exchange
   - System creates one `ReturnExchangeRequest` per selected order
   - Entire order quantity is returned/exchanged
   - No support for partial returns

### Problem Statement
- User orders 2 quantities of same product (1 order row with quantity=2)
- User wants to return/exchange only 1 quantity
- Current system doesn't support this

---

## Implementation Strategy

### Phase 1: Database Changes

#### 1.1 Add Fields to `return_exchange_requests` Table
**Migration:** `add_partial_quantity_to_return_exchange_requests_table.php`

```php
Schema::table('return_exchange_requests', function (Blueprint $table) {
    $table->unsignedInteger('requested_quantity')->default(1)->after('order_id');
    $table->unsignedInteger('returned_quantity')->default(0)->nullable()->after('requested_quantity');
    $table->unsignedInteger('remaining_quantity')->nullable()->after('returned_quantity');
});
```

**Purpose:**
- `requested_quantity`: How many units user wants to return/exchange
- `returned_quantity`: How many units actually returned (for tracking)
- `remaining_quantity`: Calculated field (order.quantity - sum of all returned quantities)

#### 1.2 Add Index for Performance
```php
$table->index(['order_id', 'status']); // For faster lookups
```

---

### Phase 2: Frontend Changes

#### 2.1 Update Return/Exchange Form (`resources/views/frontend/orders/return-exchange.blade.php`)

**Changes Required:**
1. **Add Quantity Selector for Each Item:**
   - Show current order quantity
   - Add input/select for "Quantity to Return/Exchange"
   - Default: 1, Max: order quantity
   - Show remaining returnable quantity (if partial return already exists)

2. **Display Logic:**
   ```html
   <div class="quantity-selector">
       <label>Quantity to Return/Exchange:</label>
       <input type="number" 
              name="quantities[{{ $item['id'] }}]" 
              min="1" 
              max="{{ $item['quantity'] - $item['already_returned'] }}"
              value="1"
              required>
       <small>Ordered: {{ $item['quantity'] }} | 
              Already Returned: {{ $item['already_returned'] ?? 0 }} | 
              Available: {{ $item['quantity'] - ($item['already_returned'] ?? 0) }}</small>
   </div>
   ```

3. **Validation:**
   - JavaScript validation: Cannot exceed available quantity
   - Show warning if trying to return more than available
   - Disable submit if invalid quantities

#### 2.2 Update Order Display
- Show "Already Returned" badge if partial return exists
- Show remaining quantity clearly
- Disable items that are fully returned

---

### Phase 3: Backend Controller Changes

#### 3.1 Update `requestReturnExchange()` Method
**File:** `app/Http/Controllers/Front/AuthController.php`

**Changes:**
1. **Validation:**
   ```php
   $request->validate([
       'selected_items' => 'required|array|min:1',
       'selected_items.*' => 'integer|exists:orders,id',
       'quantities' => 'required|array',
       'quantities.*' => 'required|integer|min:1',
       'reason' => 'required|string',
       'action' => 'required|in:return,exchange',
       'photo' => 'nullable|image|max:2048',
   ]);
   ```

2. **Check Available Quantity:**
   ```php
   foreach ($orders as $order) {
       $requestedQty = $request->quantities[$order->id] ?? 1;
       
       // Calculate already returned quantity
       $alreadyReturned = ReturnExchangeRequest::where('order_id', $order->id)
           ->whereIn('status', ['pending', 'approved', 'received_restocked', 'received_discarded', 'completed'])
           ->sum('requested_quantity');
       
       $availableQty = $order->quantity - $alreadyReturned;
       
       if ($requestedQty > $availableQty) {
           return back()->with('error', "Cannot return {$requestedQty} units. Only {$availableQty} available for return.");
       }
   }
   ```

3. **Create Request with Quantity:**
   ```php
   $returnRequest = ReturnExchangeRequest::create([
       'order_id' => $order->id,
       'requested_quantity' => $requestedQty,
       'type' => $request->action,
       'reason' => $request->reason,
       'photo_path' => $photoPath,
       'status' => 'pending',
       'customer_notes' => $request->reason,
   ]);
   ```

#### 3.2 Update `returnExchange()` Method
**File:** `app/Http/Controllers/Front/AuthController.php`

**Changes:**
- Calculate and pass `already_returned` quantity for each item
- Pass available quantity for display

```php
$items = $orders->map(function ($item) {
    $alreadyReturned = ReturnExchangeRequest::where('order_id', $item->id)
        ->whereIn('status', ['pending', 'approved', 'received_restocked', 'received_discarded', 'completed'])
        ->sum('requested_quantity');
    
    return [
        'id' => $item->id,
        'quantity' => $item->quantity,
        'already_returned' => $alreadyReturned,
        'available_for_return' => $item->quantity - $alreadyReturned,
        // ... other fields
    ];
});
```

---

### Phase 4: Admin Panel Changes

#### 4.1 Update Return/Exchange Request Display
**Files:**
- `resources/views/admin/master/returns/show.blade.php`
- `resources/views/admin/merchandise/returns/show.blade.php`
- `resources/views/admin/back_to_school/returns/show.blade.php`
- `resources/views/inventoryadmin/returns/show.blade.php`

**Changes:**
- Display `requested_quantity` vs `order.quantity`
- Show if it's a partial return
- Display remaining quantity on order

#### 4.2 Update `receive()` Method
**File:** `app/Http/Controllers/Admin/Master/ReturnExchangeController.php`

**Changes:**
- Restock only the `requested_quantity`, not entire order quantity
- Update `returned_quantity` field

```php
if ($data['action'] === 'restock' && $order) {
    $product = ProductMapping::where('product_name', $order->item_name)
        ->where('school_id', $order->school_id)
        ->first();
    
    if ($product) {
        $restockQty = $returnRequest->requested_quantity; // Use requested, not order quantity
        $before = $product->inventory_stock;
        $after = $before + $restockQty;
        $product->update(['inventory_stock' => $after]);
        
        // Update returned_quantity
        $returnRequest->update(['returned_quantity' => $restockQty]);
        
        InventoryAdjustment::create([
            'product_mapping_id' => $product->id,
            'quantity_change' => $restockQty,
            'reason' => 'return_restock',
            'comment' => "Restock {$restockQty} units from return for order {$order->order_number}",
            'stock_before' => $before,
            'stock_after' => $after,
        ]);
    }
}
```

#### 4.3 Update `generateExchange()` Method
**Changes:**
- Create exchange order with `requested_quantity`, not full order quantity
- Handle price calculation for partial exchange

```php
$newOrder = Order::create([
    // ... other fields
    'quantity' => $returnRequest->requested_quantity, // Use requested quantity
    'total_amount' => ($order->total_amount / $order->quantity) * $returnRequest->requested_quantity,
    'tax_amount' => ($order->tax_amount / $order->quantity) * $returnRequest->requested_quantity,
    // ...
]);
```

#### 4.4 Update `refund()` Method
**Changes:**
- Calculate refund based on `requested_quantity`, not full order

```php
$refundAmount = ($order->total_amount / $order->quantity) * $returnRequest->requested_quantity;
```

---

### Phase 5: Model Updates

#### 5.1 Update `ReturnExchangeRequest` Model
**File:** `app/Models/Admin/Master/ReturnExchangeRequest.php`

**Changes:**
```php
protected $fillable = [
    // ... existing fields
    'requested_quantity',
    'returned_quantity',
    'remaining_quantity',
];

// Add accessor for remaining quantity
public function getRemainingQuantityAttribute()
{
    if (!$this->order) {
        return null;
    }
    
    $totalReturned = ReturnExchangeRequest::where('order_id', $this->order_id)
        ->whereIn('status', ['pending', 'approved', 'received_restocked', 'received_discarded', 'completed'])
        ->sum('requested_quantity');
    
    return max(0, $this->order->quantity - $totalReturned);
}
```

---

### Phase 6: Edge Cases & Validation

#### 6.1 Prevent Over-Return
- Validation: `requested_quantity <= available_quantity`
- Check before creating request
- Check before approving request

#### 6.2 Multiple Partial Returns
- Allow multiple return requests for same order
- Track cumulative returned quantity
- Prevent total from exceeding order quantity

#### 6.3 Status Tracking
- If order has partial return:
  - Show "Partially Returned" status
  - Show remaining quantity
  - Allow additional returns until fully returned

#### 6.4 Exchange Logic
- Partial exchange: Create new order with requested quantity
- Price calculation: Proportional to original order
- Inventory: Decrement only requested quantity

---

### Phase 7: UI/UX Improvements

#### 7.1 Order History Display
- Show return status per item
- Badge: "Fully Returned", "Partially Returned", "Available for Return"
- Show quantities clearly

#### 7.2 Return Request List
- Show requested quantity vs order quantity
- Highlight partial returns
- Show remaining quantity

---

### Phase 8: Testing Checklist

#### 8.1 Test Scenarios
1. ✅ Return 1 of 2 quantities
2. ✅ Return 2 of 2 quantities (full return)
3. ✅ Return 1, then return remaining 1 (multiple partial returns)
4. ✅ Exchange 1 of 2 quantities
5. ✅ Try to return more than available (should fail)
6. ✅ Restock partial return (inventory should update correctly)
7. ✅ Refund partial return (amount should be proportional)
8. ✅ Exchange partial return (new order quantity should match)

#### 8.2 Data Integrity Checks
- ✅ Order quantity = sum of all returned quantities + remaining
- ✅ Inventory restocked correctly
- ✅ Refund amounts calculated correctly
- ✅ Exchange orders created with correct quantities

---

### Phase 9: Migration Strategy

#### 9.1 Data Migration
- Existing return requests: Set `requested_quantity = order.quantity`
- Existing return requests: Set `returned_quantity = order.quantity` (if status is received/completed)

#### 9.2 Backward Compatibility
- Default `requested_quantity = 1` for new requests
- Handle null values gracefully
- Update all admin views to show quantities

---

## Implementation Order

1. **Step 1:** Create migration for new fields
2. **Step 2:** Update ReturnExchangeRequest model
3. **Step 3:** Update frontend form (quantity selector)
4. **Step 4:** Update `requestReturnExchange()` method
5. **Step 5:** Update `returnExchange()` method (display)
6. **Step 6:** Update admin `receive()` method
7. **Step 7:** Update admin `generateExchange()` method
8. **Step 8:** Update admin `refund()` method
9. **Step 9:** Update all admin views
10. **Step 10:** Test all scenarios
11. **Step 11:** Update email templates (if needed)

---

## Risk Mitigation

### Potential Issues:
1. **Data Inconsistency:** Multiple partial returns could exceed order quantity
   - **Solution:** Strict validation at every step

2. **Inventory Mismatch:** Restocking wrong quantity
   - **Solution:** Always use `requested_quantity` for restocking

3. **Refund Calculation:** Wrong refund amount
   - **Solution:** Proportional calculation based on unit price

4. **Exchange Price:** Price mismatch for partial exchange
   - **Solution:** Calculate proportionally or use current price (define policy)

---

## Notes

- All quantity fields should be unsigned integers
- Always validate quantities before database operations
- Use database transactions for critical operations
- Add logging for partial return operations
- Update audit logs to include quantity information


