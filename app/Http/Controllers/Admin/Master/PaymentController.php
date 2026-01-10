<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order' => fn($q) => $q->withoutGlobalScopes()]);

        // Search (Order Number or Payment ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function($subQ) use ($search) {
                      $subQ->withoutGlobalScopes()->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by Payment Method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by Date From
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter by Date To
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(10);
        $routePrefix = 'master.admin';
        
        return view('admin.master.payments.index', compact('payments', 'routePrefix'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order' => fn($q) => $q->withoutGlobalScopes()]); // Ensure order is loaded
        $routePrefix = 'master.admin';
        return view('admin.master.payments.show', compact('payment', 'routePrefix'));
    }
}
