<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Handle newsletter subscription submissions
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'source' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.'
            ], 422);
        }

        ContactSubmission::create([
            'form_type' => 'newsletter',
            'name' => $request->input('name') ?? 'Subscriber',
            'email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'metadata' => [
                'source_page' => $request->input('source', 'unknown'),
                'accepted_policy' => true
            ],
            'status' => 'new'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed!'
        ]);
    }

    /**
     * Handle contact form submissions (general/project)
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
            'phone' => 'nullable|string|max:25',
            'form_type' => 'required|in:general,project,newsletter',
            'source' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill all required fields correctly.'
            ], 422);
        }

        ContactSubmission::create([
            'form_type' => $request->input('form_type'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
            'ip_address' => $request->ip(),
            'metadata' => [
                'source_page' => $request->input('source', 'unknown')
            ],
            'status' => 'new'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully!'
        ]);
    }
}
