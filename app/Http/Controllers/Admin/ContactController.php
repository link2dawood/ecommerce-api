<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with('user')->latest();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter by status (if you add status column)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $contacts = $query->paginate(15);

        return view('admin.contacts.index', compact('contacts'));
    }

    public function show($id)
    {
        $contact = Contact::with('user')->findOrFail($id);
        
        // Mark as read (if you add is_read column)
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact message deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contacts,id'
        ]);

        Contact::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected messages deleted successfully.'
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Marked as read.'
        ]);
    }

    public function markAsUnread(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Marked as unread.'
        ]);
    }
}