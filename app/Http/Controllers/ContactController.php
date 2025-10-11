<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display contact page (frontend) or JSON list (API).
     */
    public function index(Request $request)
    {
        // ✅ If it's an API request (via Sanctum / fetch / Postman)
        if ($request->expectsJson() || $request->is('api/*')) {
            $query = Contact::with('user')->latest();

            // Filter by read/unread status
            if ($request->has('status')) {
                if ($request->status === 'unread') {
                    $query->unread();
                } elseif ($request->status === 'read') {
                    $query->read();
                }
            }

            $contacts = $query->paginate($request->get('per_page', 15));
            return response()->json($contacts, 200);
        }

        // ✅ Otherwise, return a Blade view for web users
        return view('frontend.contact.index');
    }

    /**
     * Store a new contact message (Sanctum + normal form supported)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            // ✅ Return JSON for API calls
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // ✅ Return validation errors for web
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $contact = Contact::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'subject' => $request->subject ?? 'General Inquiry',
                'message' => $request->message,
                'user_id' => Auth::id(),
                'is_read' => false,
            ]);

            // Send email notification to admin
            $this->sendEmailNotification($contact);

            // ✅ Return JSON for API calls
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your message has been sent successfully!',
                    'data'    => $contact
                ], 201);
            }

            // ✅ Redirect back for normal form submissions
            return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');

        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());

            // ✅ Return JSON for API calls
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message. Please try again later.'
                ], 500);
            }

            return back()
                ->with('error', 'There was an error sending your message. Please try again later.')
                ->withInput();
        }
    }

    /**
     * Show single message (API and Admin use)
     */
    public function show($id)
    {
        $contact = Contact::with('user')->findOrFail($id);

        // Mark as read when viewed
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json($contact, 200);
        }

        // Return admin view
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Mark message as read/unread
     */
    public function toggleRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => !$contact->is_read]);

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $contact
            ], 200);
        }

        return back()->with('success', 'Message status updated successfully');
    }

    /**
     * Mark multiple messages as read
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contacts,id'
        ]);

        Contact::whereIn('id', $request->ids)->update(['is_read' => true]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read'
            ], 200);
        }

        return back()->with('success', 'Messages marked as read');
    }

    /**
     * Delete message (API use, Sanctum protected)
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ], 200);
        }

        return back()->with('success', 'Message deleted successfully');
    }

    /**
     * Bulk delete messages
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contacts,id'
        ]);

        Contact::whereIn('id', $request->ids)->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Messages deleted successfully'
            ], 200);
        }

        return back()->with('success', 'Messages deleted successfully');
    }

    /**
     * Get contact statistics (Admin)
     */
    public function statistics()
    {
        $stats = [
            'total_messages' => Contact::count(),
            'unread_messages' => Contact::unread()->count(),
            'read_messages' => Contact::read()->count(),
            'messages_today' => Contact::whereDate('created_at', today())->count(),
            'messages_this_week' => Contact::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'messages_this_month' => Contact::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats, 200);
    }

    /**
     * Admin: View all messages
     */
    public function adminIndex(Request $request)
    {
        $query = Contact::with('user')->latest();

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $contacts = $query->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($contacts, 200);
        }

        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Send email notification to admin
     */
    protected function sendEmailNotification($contact)
    {
        try {
            $data = [
                'name' => $contact->name,
                'email' => $contact->email,
                'subject' => $contact->subject,
                'message' => $contact->message,
            ];

            Mail::send('emails.contact', $data, function($message) use ($data) {
                $message->to(config('mail.from.address'))
                    ->subject('New Contact Form Submission: ' . $data['subject'])
                    ->replyTo($data['email'], $data['name']);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send contact email: ' . $e->getMessage());
        }
    }

    /**
     * Reply to contact message (Admin)
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string|max:5000'
        ]);

        $contact = Contact::findOrFail($id);

        try {
            Mail::send('emails.contact-reply', ['message' => $request->reply_message, 'contact' => $contact], function($message) use ($contact) {
                $message->to($contact->email)
                    ->subject('Re: ' . $contact->subject);
            });

            $contact->update(['is_read' => true]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reply sent successfully'
                ], 200);
            }

            return back()->with('success', 'Reply sent successfully');

        } catch (\Exception $e) {
            \Log::error('Failed to send reply: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send reply'
                ], 500);
            }

            return back()->with('error', 'Failed to send reply');
        }
    }
}