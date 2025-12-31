<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Mail\ContactNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Store contact message - all fields are optional
        $contact = Contact::create([
            'firstname' => $request->firstname ?? null,
            'lastname' => $request->lastname ?? null,
            'email' => $request->email ?? null,
            'phone' => $request->number ?? null,
            'message' => $request->message ?? null,
        ]);

        // Send email notification to ragunath7272@gmail.com
        try {
            Mail::to('ragunath7272@gmail.com')->send(new ContactNotificationMail($contact));
            Log::info('Contact form notification email sent to ragunath7272@gmail.com', [
                'contact_id' => $contact->id
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('Failed to send contact form notification email', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }

        // Return success message for AJAX response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json('Thank you for contacting us! We will get back to you soon.');
        }

        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
