<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Merchandise\PrintJob;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PrintQueueController extends Controller
{
    public function index(Request $request): View
    {
        $query = PrintJob::with(['order', 'product'])->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $printJobs = $query->paginate(20);

        return view('admin.merchandise.print_queue.index', compact('printJobs'));
    }

    public function show($id): View
    {
        $printJob = PrintJob::with(['order', 'product'])->findOrFail($id);
        return view('admin.merchandise.print_queue.show', compact('printJob'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $printJob = PrintJob::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:pending,printing,completed,cancelled',
        ]);

        $printJob->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Print Job status updated.');
    }
}
