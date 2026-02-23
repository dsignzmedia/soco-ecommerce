<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $table = 'privacy_policy';

    protected $fillable = [
        'content',
    ];

    public static function current(): self
    {
        $policy = static::first();

        if (!$policy) {
            $defaultContent = '<h3>1. Introduction</h3>
<p>Welcome to TheSkoolStore. Your privacy is important to us, and we are committed to safeguarding the personal information you share with us. This Privacy Policy explains how we collect, use, store, and protect your data when you interact with our website or services.</p>
<h3>2. Information We Collect</h3>
<h4>Personal Information</h4>
<p>We may collect the following personal details:</p>
<ul>
<li>Name, email address, phone number, and delivery address</li>
<li>Payment details (processed securely through trusted third-party payment providers)</li>
</ul>
<h4>Non-Personal Information</h4>
<p>We may also collect:</p>
<ul>
<li>IP address, browser type, and device information</li>
<li>Cookies and usage data to help improve website functionality and user experience</li>
</ul>
<h3>3. How We Use Your Information</h3>
<p>Your information is used solely for purposes such as:</p>
<ul>
<li>Processing and fulfilling your orders</li>
<li>Enhancing and improving website performance and customer experience</li>
<li>Sharing updates, offers, and promotional communications (only with your consent)</li>
<li>Meeting legal or regulatory requirements</li>
</ul>
<h3>4. Payment Security &amp; Data Protection</h3>
<ul>
<li>We do not store or process your payment information directly. All transactions are handled securely by third-party payment processors compliant with industry security standards.</li>
<li>We use encryption, secure servers, and other protective measures to safeguard your data.</li>
</ul>
<h3>5. Sharing Your Information</h3>
<p>We may share your information only with trusted partners when necessary:</p>
<ul>
<li>Payment processors to securely complete your transactions</li>
<li>Delivery partners to ensure timely order fulfillment</li>
<li>Legal authorities, but only if required by law</li>
</ul>
<p>We do not sell or rent your information to third parties.</p>
<h3>6. Your Rights</h3>
<p>You have the right to access, correct, or request deletion of your personal data. For any such requests, please contact: <a href="mailto:hello@theskoolstore.com">hello@theskoolstore.com</a></p>
<h3>7. Cookies &amp; Tracking Technologies</h3>
<p>Our website uses cookies to improve functionality. You may adjust your browser settings to manage or disable cookies.</p>
<h3>8. Updates to This Privacy Policy</h3>
<p>We may update this policy periodically. Any changes will be posted on this page with an updated effective date.</p>';

            $policy = static::create(['content' => $defaultContent]);
        }

        return $policy;
    }
}
