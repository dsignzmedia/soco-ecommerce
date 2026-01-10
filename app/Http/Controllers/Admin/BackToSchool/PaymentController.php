<?php

namespace App\Http\Controllers\Admin\BackToSchool;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order' => fn($q) => $q->withoutGlobalScopes()])->where('product_type', 'back_to_school');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_id', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($subQ) use ($search) {
                        $subQ->withoutGlobalScopes()->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(10);
        $routePrefix = 'admin.back_to_school';
        $layout = 'admin.layouts.back_to_school';

        return view('admin.master.payments.index', compact('payments', 'routePrefix', 'layout'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order' => fn($q) => $q->withoutGlobalScopes()]);
        abort_if($payment->product_type !== 'back_to_school', 404);

        $routePrefix = 'admin.back_to_school';
        $layout = 'admin.layouts.back_to_school';
        return view('admin.master.payments.show', compact('payment', 'routePrefix', 'layout'));
    }
}

