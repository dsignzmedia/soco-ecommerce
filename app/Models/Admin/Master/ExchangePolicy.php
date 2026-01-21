<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangePolicy extends Model
{
    use HasFactory;

    protected $table = 'exchange_policy';

    protected $fillable = [
        'content',
    ];

    public static function current(): self
    {
        $policy = static::first();
        
        if (!$policy) {
            // Default content if none exists
            $defaultContent = '<p>At The SkoolStore, we want every parent and student to have a smooth and satisfying shopping experience. If something isn\'t right with your order, we\'re here to help.</p>
<h3>1. Eligibility for Exchange</h3>
<p>You can request an exchange if:</p>
<ul>
<li>The product received is damaged or defective</li>
<li>The size does not fit</li>
<li>You received the wrong item</li>
</ul>
<p>(All requests will be reviewed, and only after approval, the exchange process will move forward.)</p>
<h3>2. Conditions</h3>
<p>To qualify:</p>
<ul>
<li>Items must be unused, unwashed, and in original condition</li>
<li>Tags and packaging must be intact</li>
<li>Request must be raised within 2 days of delivery</li>
</ul>
<h3>3. Exchange Process</h3>
<ul>
<li>Choose the correct size or product you want to exchange for</li>
<li>Our team will arrange pickup if applicable (based on location)</li>
<li>Replacement will be processed once the original product is received and inspected</li>
</ul>
<p>(All requests will be reviewed, and only after approval, the exchange process will move forward.)</p>
<h3>4. Pickup &amp; Delivery</h3>
<ul>
<li>Pickup availability depends on your area</li>
<li>If pickup is not available, customers may need to ship the product to our address&mdash;details will be provided by our team</li>
</ul>
<h3>5. Contact Us</h3>
<p>For any exchange request, please contact our support team at: <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>';
            
            $policy = static::create([
                'content' => $defaultContent,
            ]);
        }
        
        return $policy;
    }
}

