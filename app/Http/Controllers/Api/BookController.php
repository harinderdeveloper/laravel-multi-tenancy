<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($branch_id)
    {
        // Fetch all books for the tenant (branch)
        $books = Book::all();
        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $branch_id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
        ]);
        $book = Book::create($validated);
        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($branch_id, Book $book)
    {
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $branch_id, Book $book)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
        ]);
        $book->update($validated);
        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($branch_id, Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }
}
