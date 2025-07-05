<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use App\Services\BranchService;
use Illuminate\Validation\ValidationException;

class BranchController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();
        return response()->json($branches);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'school_id' => 'required|exists:schools,id',
                'name' => 'required|string|max:255|unique:branches,name',
                'location' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            $branch = $this->branchService->createBranch($validated);
            return response()->json($branch, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create branch or migrate tenant DB', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        return response()->json($branch);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        try {
            $validated = $request->validate([
                'school_id' => 'sometimes|required|exists:schools,id',
                'name' => 'sometimes|required|string|max:255|unique:branches,name,' . $branch->id,
                'db_name' => 'sometimes|required|string|max:255',
                'db_username' => 'sometimes|required|string|max:255',
                'db_password' => 'sometimes|required|string|max:255',
                'location' => 'sometimes|required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            $branch->update($validated);
            return response()->json($branch);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update branch', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return response()->json(['message' => 'Branch deleted successfully']);
    }
} 