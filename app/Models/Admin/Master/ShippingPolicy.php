<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingPolicy extends Model
{
    use HasFactory;

    protected $table = 'shipping_policy';

    protected $fillable = [
        'content',
    ];

    public static function current(): self
    {
        $policy = static::first();

        if (!$policy) {
            $defaultContent = '<h3>1. Order Processing</h3>
<p>Orders are processed within 3-5 business days (excluding weekends and holidays) after receiving payment confirmation.</p>
<p>Once your order is processed, you will receive a confirmation email with tracking details.</p>
<h3>2. Shipping Charges</h3>
<p>Shipping charges will be calculated at checkout based on your location and order value.</p>
<h3>3. Estimated Delivery Time</h3>
<ul>
<li>Within Coimbatore: 3-5 business days.</li>
<li>Other Cities in India: 3-7 business days.</li>
</ul>
<p>Delivery times may vary based on location, weather conditions, and courier service availability.</p>
<h3>4. Tracking Your Order</h3>
<p>Once your order is shipped, you will receive a tracking link via email/SMS to monitor your shipment\'s progress.</p>
<h3>5. Shipping Partners</h3>
<p>We use reliable courier services to ensure timely and safe delivery.</p>
<h3>6. Order Issues</h3>
<p>If you receive a damaged or incorrect item, please contact us at <a href="tel:+919600833114">+91 9600833114</a> within 2 days of delivery.</p>
<h3>7. Address &amp; Delivery Changes</h3>
<p>Ensure your shipping address is correct at checkout. We are unable to change the address once the order is shipped.</p>
<h3>8. Contact Us</h3>
<p>For any shipping-related queries, reach out to us at <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a> or call us at <a href="tel:+919600833114">+91 9600833114</a></p>';

            $policy = static::create(['content' => $defaultContent]);
        }

        return $policy;
    }
}
