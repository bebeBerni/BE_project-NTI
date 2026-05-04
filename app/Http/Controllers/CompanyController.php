<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
class CompanyController  extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Company::with('users')->get();
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
    $this->authorize('create', Company::class);

    $validated = $request->validate([
        'company_name' => 'required|string',
        'ico' => 'required|string',
        'description' => 'nullable|string',
        'website' => 'required|string',
        'address' => 'nullable|string',
    ]);

    $company = Company::create($validated);

    return response()->json($company);
}

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
         return Company::with('users')->findOrFail($id);
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
         $company = Company::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'required|string',
            'ico' => 'required|string',
            'description' => 'nullable|string',
            'website' => 'required|string',
            'address' => 'nullable|string',
        ]);
        $company->update($validated);

        return response()->json($company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         Company::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
