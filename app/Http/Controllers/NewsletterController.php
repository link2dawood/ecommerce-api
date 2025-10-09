<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Response;

class NewsletterController extends Controller
{
    public function index()
    {
        $newsletters = Newsletter::latest()->paginate(15);
        return view('admin.newsletters.index', compact('newsletters'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
        ]);

        Newsletter::create([
            'email' => $request->email,
        ]);

        return back()->with('success', 'Thank you for subscribing!');
    }

    public function destroy($id)
    {
        try {
            $newsletter = Newsletter::findOrFail($id);
            $newsletter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subscriber deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting subscriber'
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:newsletters,id'
        ]);

        try {
            Newsletter::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' subscriber(s) deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting subscribers'
            ], 500);
        }
    }

    public function export()
    {
        $newsletters = Newsletter::all();
        
        $filename = 'newsletter-subscribers-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($newsletters) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['ID', 'Email', 'Subscribed At']);
            
            // Add data rows
            foreach ($newsletters as $newsletter) {
                fputcsv($file, [
                    $newsletter->id,
                    $newsletter->email,
                    $newsletter->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}