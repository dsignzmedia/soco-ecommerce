<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ExchangePolicy;
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
        return view('frontend.policies.privacy-policy');
    }

    public function shippingPolicy()
    {
        return view('frontend.policies.shipping-policy');
    }

    public function termsConditions()
    {
        return view('frontend.policies.terms-conditions');
    }
}

