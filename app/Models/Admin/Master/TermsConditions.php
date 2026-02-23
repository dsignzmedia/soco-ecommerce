<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsConditions extends Model
{
    use HasFactory;

    protected $table = 'terms_conditions';

    protected $fillable = [
        'content',
    ];

    public static function current(): self
    {
        $policy = static::first();

        if (!$policy) {
            $defaultContent = '<h3>1. Introduction</h3>
<p>Welcome to TheSkoolStore. By using our website, you agree to these Terms &amp; Conditions.</p>
<h3>2. Use of Website</h3>
<ul>
<li>Users must provide accurate information when placing orders.</li>
<li>Any misuse of the website (fraud, illegal activities) is strictly prohibited.</li>
<li>Bulk orders may be subject to different terms and conditions, which will be communicated at the time of order.</li>
</ul>
<h3>3. Orders, Pricing &amp; Payments</h3>
<ul>
<li>Prices are subject to change without prior notice.</li>
<li>Payments must be made via approved methods on our platform.</li>
<li>We do not store payment details; transactions are securely handled by third-party payment processors.</li>
</ul>
<h3>4. Shipping &amp; Exchanges</h3>
<ul>
<li>Shipping timelines are estimates and may vary based on availability.</li>
<li>Please check the size chart and video tutorials before placing an order. If you receive a defective or incorrect product, please contact us within 2 days for assistance.</li>
<li>Exchanges are accepted within 2 days if items meet exchange criteria.</li>
</ul>
<h3>5. Limitation of Liability</h3>
<ul>
<li>TheSkoolStore is not liable for indirect damage, delays, or third-party failures.</li>
<li>Our liability is limited to the amount paid for the order.</li>
</ul>
<h3>6. Dispute Resolution</h3>
<ul>
<li>Any disputes will first be resolved through informal negotiation.</li>
<li>If unresolved, arbitration will be the next step.</li>
</ul>
<h3>7. Modifications to Terms</h3>
<p>We may update these terms. Continued use of the website means you accept the revised terms.</p>
<h3>8. Contact Us</h3>
<p>For questions, reach out to <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>';

            $policy = static::create(['content' => $defaultContent]);
        }

        return $policy;
    }
}
