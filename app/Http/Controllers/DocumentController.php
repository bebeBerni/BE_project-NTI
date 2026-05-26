<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_application_id' => ['required', 'string', 'max:45'],
            'type' => ['required', 'string', 'max:45'],
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:5120'
            ],
        ]);

        $file = $request->file('file');

        // Store file in storage/app/public/documents
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'user_id' => $request->user()->id,
            'project_application_id' => $validated['project_application_id'],
            'type' => $validated['type'],

            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,

            'upload_at' => now(),
        ]);

        return response()->json([
            'message' => 'File uploaded successfully.',
            'document' => $document,

            // Public URL
            'url' => Storage::disk('public')->url($path),
        ], 201);
    }
}
