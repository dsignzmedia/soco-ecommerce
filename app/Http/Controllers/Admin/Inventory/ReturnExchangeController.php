<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ReturnExchangeRequest;
use Illuminate\View\View;

class ReturnExchangeController extends Controller
{
    public function index(): View
    {
        $requests = ReturnExchangeRequest::with('order')->latest()->paginate(20);
        return view('inventoryadmin.returns.index', compact('requests'));
    }
}