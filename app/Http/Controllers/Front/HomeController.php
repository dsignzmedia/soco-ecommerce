<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('frontend.index');
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
        return view('frontend.policies.return-exchange');
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

