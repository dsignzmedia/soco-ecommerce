<?php

namespace App\Exports;

use App\Models\Admin\Master\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    protected $modelClass;

    public function __construct(array $filters, $modelClass = null)
    {
        $this->filters = $filters;
        $this->modelClass = $modelClass ?? Order::class;
    }

    public function collection()
    {
        $query = $this->modelClass::with(['school', 'product'])
            ->latest();

        // Apply filters dynamically
        $filters = $this->filters;
        
        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }
        if (!empty($filters['grade'])) {
            $query->where('grade', $filters['grade']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['product_type'])) {
            $query->where('product_type', 'like', $filters['product_type'] . '%');
        }
        
        // Handle 'order_status' vs 'status' inconsistencies if any, though controllers seem to use 'order_status' db column
        if (!empty($filters['order_status'])) {
            $query->where('order_status', $filters['order_status']);
        } elseif (!empty($filters['status'])) { 
             // Fallback for BTS/Merch if they pass 'status' in filters array directly
            $query->where('order_status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }
        if (!empty($filters['order_number'])) {
            $query->where('order_number', 'like', '%' . $filters['order_number'] . '%');
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('order_date', '>=', \Carbon\Carbon::parse($filters['date_from']));
        }
        // Note: BTS/Merch controllers check 'created_at' in their index, but Master check 'order_date'. 
        // IMPORTANT: Let's check both or use created_at if order_date is null? 
        // To be safe, let's stick to what Master does (order_date) but fallback or check. 
        // Actually, let's use the property that is common. 
        // Master Controller uses `order_date`. BTS/Merch use `created_at`.
        // Let's modify logic to optionally check created_at if order_date column doesn't exist? No, that's complex.
        // Let's assume order_date exists on all or use created_at?
        // Let's check Master Order model... it uses order_date.
        // BTS/Merch probably uses created_at in index method.
        // Let's try to handle both or pick one.
        // If I use $this->modelClass, I can't easily know which column to filter without inspection.
        // But I can check if 'date_from' exists and filter on created_at if it's BTS/Merch?
        // Or simply add:
        if (!empty($filters['date_from'])) {
             // If model is Master, use order_date. If others...
             // Let's blindly try order_date OR created_at depending on model?
             // Checking class name:
             if (str_contains($this->modelClass, 'BackToSchool') || str_contains($this->modelClass, 'Merchandise')) {
                 $query->whereDate('created_at', '>=', \Carbon\Carbon::parse($filters['date_from']));
             } else {
                 $query->whereDate('order_date', '>=', \Carbon\Carbon::parse($filters['date_from']));
             }
        }
        
        if (!empty($filters['date_to'])) {
             if (str_contains($this->modelClass, 'BackToSchool') || str_contains($this->modelClass, 'Merchandise')) {
                 $query->whereDate('created_at', '<=', \Carbon\Carbon::parse($filters['date_to']));
             } else {
                 $query->whereDate('order_date', '<=', \Carbon\Carbon::parse($filters['date_to']));
             }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Order Date',
            'School',
            'Student Name',
            'Grade',
            'Product Name',
            'Category',
            'Product Type',
            'Size',
            'Quantity',
            'Total Amount',
            'Payment Status',
            'Order Status',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Shipping Address',
            'Tracking Number'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->order_date ? $order->order_date->format('Y-m-d') : '',
            $order->school ? $order->school->name : '',
            $order->student_name,
            $order->grade,
            $order->item_name,
            $order->category,
            $order->product_type,
            $order->size,
            $order->quantity,
            $order->total_amount,
            ucfirst($order->payment_status),
            ucfirst(str_replace('_', ' ', $order->order_status)),
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->shipping_address . ', ' . $order->shipping_city . ', ' . $order->shipping_state . ' - ' . $order->shipping_zip,
            $order->tracking_number
        ];
    }
}
