<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = ContactSubmission::orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $contacts = $query->paginate(20);
        $counts = [
            'all' => ContactSubmission::count(),
            'new' => ContactSubmission::where('status', 'new')->count(),
            'read' => ContactSubmission::where('status', 'read')->count(),
            'replied' => ContactSubmission::where('status', 'replied')->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'counts', 'status'));
    }

    public function show(ContactSubmission $contact)
    {
        if ($contact->status === 'new') {
            $contact->markAsRead();
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function export()
    {
        $contacts = ContactSubmission::all();
        $fileName = 'contacts_export_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Date', 'Type', 'Origin', 'Name', 'Email', 'Phone', 'Message', 'Status'];

        $callback = function() use($contacts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->created_at->format('Y-m-d H:i:s'),
                    $contact->form_type,
                    $contact->metadata['source_page'] ?? 'Unknown',
                    $contact->name,
                    $contact->email,
                    $contact->phone,
                    $contact->message,
                    $contact->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
