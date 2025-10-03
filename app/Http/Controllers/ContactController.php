<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // List all contacts (admin use)
    public function index()
    {
        $contacts = Contact::with('user')->latest()->get();
        return response()->json($contacts, 200);
    }

    // Store contact message
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

        return response()->json([
            'message' => 'Your message has been sent successfully!',
            'data'    => $contact
        ], 201);
    }

    // Show single message
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json($contact, 200);
    }

    // Delete message
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Message deleted successfully'], 200);
    }
}
