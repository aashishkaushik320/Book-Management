<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Storage;
class BookController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            $query = Book::query();

          
            // dd($request->all());
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('author', 'like', '%' . $search . '%');
                });
            }

            // dd($query->get());
            if ($request->filled('title')) {
                $query->where('title', 'like', '%' . $request->input('title') . '%');
            }

            if ($request->filled('author')) {
                $query->where('author', 'like', '%' . $request->input('author') . '%');
            }

            $paginationValue = 10;
            if ($request->filled('per_page')) {
                $paginationValue = $request->per_page;
            }

            $books = $query->paginate($paginationValue);

            $books->getCollection()->transform(function ($book) {
                return $this->formatBookCover($book);
            });

            return response()->json($books);

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve books.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

   
    public function store(StoreBookRequest $request)
    {
        // return $request->all();

        DB::beginTransaction();

        try {
            $validated = $request->validated();

            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $imagePath = $image->store('books', 'public');
                // dd($imagePath);
                $validated['cover_image'] = $imagePath;
            }

            // dd($validated);

            $book = Book::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Book created successfully.',
                'data' => $this->formatBookCover($book)
            ], 201);

        } catch (Throwable $th) {
            // return $th->getMessage();
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create book.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific book.
     */
    public function show($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);
        }

        return response()->json([
            'data' => $this->formatBookCover($book)
        ]);
    }

    
    public function update(UpdateBookRequest $request, $id)
    {
        $book = Book::findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            if ($request->hasFile('cover_image')) {
                
                if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                    Storage::disk('public')->delete($book->cover_image);
                }

                $image = $request->file('cover_image');
                $imagePath = $image->store('books', 'public');
                $validated['cover_image'] = $imagePath;
            }

            // DB::enableQueryLog();
            $book->update($validated);
            // dd(DB::getQueryLog());

            DB::commit();

            return response()->json([
                'message' => 'Book updated successfully.',
                'data' => $this->formatBookCover($book)
            ]);

        } catch (Throwable $th) {
            // return response()->json('error'=>$th->getMessage());
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update book, database transaction rolled back.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

   
    public function destroy($id)
    {
        $findBook = Book::find($id);
        // dd($findBook);
        if (!$findBook) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $findBook->delete();
            DB::commit();

            return response()->json([
                'message' => 'Book successfully deleted.'
            ]);

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

   
    private function formatBookCover(Book $book): Book
{
    if ($book->cover_image) {
        $book->cover_image = asset('storage/' . $book->cover_image);
    }

    return $book;
}
}
