<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * BookController
 * 
 * Handles library book management for librarians.
 */
class BookController extends Controller
{
    /**
     * Display books list.
     */
    public function index(Request $request)
    {
        $categories = LibraryCategory::where('is_active', true)->orderBy('name')->get();
        
        $query = LibraryBook::with('category');
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->availability) {
            if ($request->availability === 'available') {
                $query->where('available_quantity', '>', 0);
            } elseif ($request->availability === 'unavailable') {
                $query->where('available_quantity', '<=', 0);
            }
        }
        
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('author', 'like', "%{$request->search}%")
                  ->orWhere('isbn', 'like', "%{$request->search}%");
            });
        }
        
        $books = $query->orderBy('title')->paginate(20);
        
        return view('librarian.books.index', compact('books', 'categories'));
    }

    /**
     * Show create book form.
     */
    public function create()
    {
        $categories = LibraryCategory::where('is_active', true)->orderBy('name')->get();
        return view('librarian.books.create', compact('categories'));
    }

    /**
     * Store new book.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:library_books,isbn',
            'category_id' => 'required|exists:library_categories,id',
            'author' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:100',
            'publish_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'rack_number' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:50',
            'pages' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            LibraryBook::create([
                'title' => $request->title,
                'isbn' => $request->isbn,
                'category_id' => $request->category_id,
                'author' => $request->author,
                'publisher' => $request->publisher,
                'edition' => $request->edition,
                'publish_year' => $request->publish_year,
                'quantity' => $request->quantity,
                'available_quantity' => $request->quantity,
                'price' => $request->price,
                'rack_number' => $request->rack_number,
                'language' => $request->language,
                'pages' => $request->pages,
                'description' => $request->description,
                'is_active' => true,
            ]);

            DB::commit();
            return redirect()->route('librarian.books.index')->with('success', 'Book added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add book. Please try again.');
        }
    }

    /**
     * Show book details.
     */
    public function show($id)
    {
        $book = LibraryBook::with(['category', 'issues' => function ($q) {
            $q->with(['member.user'])->orderBy('issue_date', 'desc')->take(10);
        }])->findOrFail($id);
        
        return view('librarian.books.show', compact('book'));
    }

    /**
     * Show edit book form.
     */
    public function edit($id)
    {
        $book = LibraryBook::findOrFail($id);
        $categories = LibraryCategory::where('is_active', true)->orderBy('name')->get();
        return view('librarian.books.edit', compact('book', 'categories'));
    }

    /**
     * Update book.
     */
    public function update(Request $request, $id)
    {
        $book = LibraryBook::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:library_books,isbn,' . $id,
            'category_id' => 'required|exists:library_categories,id',
            'author' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:100',
            'publish_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'rack_number' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:50',
            'pages' => 'nullable|integer|min:1',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $issuedCount = $book->quantity - $book->available_quantity;
            $newAvailable = $request->quantity - $issuedCount;
            
            if ($newAvailable < 0) {
                return back()->with('error', 'Cannot reduce quantity below the number of issued books.');
            }

            $book->update([
                'title' => $request->title,
                'isbn' => $request->isbn,
                'category_id' => $request->category_id,
                'author' => $request->author,
                'publisher' => $request->publisher,
                'edition' => $request->edition,
                'publish_year' => $request->publish_year,
                'quantity' => $request->quantity,
                'available_quantity' => $newAvailable,
                'price' => $request->price,
                'rack_number' => $request->rack_number,
                'language' => $request->language,
                'pages' => $request->pages,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            DB::commit();
            return redirect()->route('librarian.books.index')->with('success', 'Book updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update book. Please try again.');
        }
    }

    /**
     * Delete book.
     */
    public function destroy($id)
    {
        $book = LibraryBook::findOrFail($id);
        
        if ($book->available_quantity < $book->quantity) {
            return back()->with('error', 'Cannot delete book with active issues.');
        }
        
        DB::beginTransaction();
        try {
            $book->delete();
            DB::commit();
            return redirect()->route('librarian.books.index')->with('success', 'Book deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete book. Please try again.');
        }
    }
}
