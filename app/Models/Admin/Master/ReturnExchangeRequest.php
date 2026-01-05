<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnExchangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'requested_quantity',
        'returned_quantity',
        'type',
        'status',
        'reason',
        'admin_notes',
        'exchange_product_name',
        'exchange_size',
        'new_order_id',
        'photo_path',
        'customer_notes',
    ];
    
    protected $casts = [
        'requested_quantity' => 'integer',
        'returned_quantity' => 'integer',
        'photo_path' => 'array', // Cast photo_path as array to handle multiple photos
    ];
    
    /**
     * Get photo paths as array (handles both JSON string and array)
     */
    public function getPhotoPathsAttribute()
    {
        if (empty($this->photo_path)) {
            return [];
        }
        
        // If already an array, return it
        if (is_array($this->photo_path)) {
            return $this->photo_path;
        }
        
        // If it's a JSON string, decode it
        $decoded = json_decode($this->photo_path, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        
        // If it's a single string path (old format), return as array
        return [$this->photo_path];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get remaining quantity that can be returned for this order
     */
    public function getRemainingQuantityAttribute()
    {
        if (!$this->order) {
            return null;
        }
        
        $totalReturned = self::where('order_id', $this->order_id)
            ->whereIn('status', ['pending', 'approved', 'received_restocked', 'received_discarded', 'completed'])
            ->sum('requested_quantity');
        
        return max(0, $this->order->quantity - $totalReturned);
    }
    
    /**
     * Check if this is a partial return
     */
    public function isPartialReturn(): bool
    {
        if (!$this->order) {
            return false;
        }
        
        return $this->requested_quantity < $this->order->quantity;
    }
}
