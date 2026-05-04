<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
           return Document::with(['user', 'projectApplication'])->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'type' => 'required|string',
        'file_name' => 'required|string',
        'file_path' => 'required|string',
        'project_application_id' => 'nullable|exists:project_applications,id',
    ]);

    $validated['user_id'] = auth()->id();

    $document = Document::create($validated);

    return response()->json($document);
}

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
          return Document::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $document->update($request->all());

        return response()->json($document);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Document::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
