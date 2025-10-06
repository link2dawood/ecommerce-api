<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Display contact page (frontend) or JSON list (API).
     */
    public function index(Request $request)
    {
        // ✅ If it's an API request (via Sanctum / fetch / Postman)
        if ($request->expectsJson() || $request->is('api/*')) {
            $contacts = Contact::with('user')->latest()->get();
            return response()->json($contacts, 200);
        }

        // ✅ Otherwise, return a Blade view for web users
        return view('frontend.contact');
    }

    /**
     * Store a new contact message (Sanctum + normal form supported)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'user_id' => Auth::id(),
        ]);

        // ✅ Return JSON for API calls
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Your message has been sent successfully!',
                'data'    => $contact
            ], 201);
        }

        // ✅ Redirect back for normal form submissions
        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }

    /**
     * Show single message (API use)
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json($contact, 200);
    }

    /**
     * Delete message (API use, Sanctum protected)
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Message deleted successfully'], 200);
    }
}
