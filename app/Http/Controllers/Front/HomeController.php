<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ExchangePolicy;
use App\Models\Admin\Master\PrivacyPolicy;
use App\Models\Admin\Master\ShippingPolicy;
use App\Models\Admin\Master\TermsConditions;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch public products (Merchandised) for homepage display
        // Fetch public products (Merchandised AND BTS) for homepage display per user request
        $publicProducts = \App\Models\Admin\Master\ProductMapping::whereIn('product_type', ['merchandised', 'back_to_school'])
            ->where('status', 'live')
            ->latest()
            ->take(8)
            ->get();

        return view('frontend.index', compact('publicProducts'));
    }

    public function getStarted()
    {
        return view('frontend.get-started');
    }

    public function aboutUs()
    {
        return view('frontend.about-us');
    }

    public function contact()
    {
        return view('frontend.contact');
    }
    public function faq()
    {
        return view('frontend.faq');
    }

    public function services()
    {
        return view('frontend.services');
    }

    public function returnExchange()
    {
        $policy = ExchangePolicy::current();
        return view('frontend.policies.return-exchange', compact('policy'));
    }

    public function privacyPolicy()
    {
        $policy = PrivacyPolicy::current();
        return view('frontend.policies.privacy-policy', compact('policy'));
    }

    public function shippingPolicy()
    {
        $policy = ShippingPolicy::current();
        return view('frontend.policies.shipping-policy', compact('policy'));
    }

    public function termsConditions()
    {
        $policy = TermsConditions::current();
        return view('frontend.policies.terms-conditions', compact('policy'));
    }
}

