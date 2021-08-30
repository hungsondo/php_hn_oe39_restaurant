<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Models\Image;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::with('category')->orderBy('updated_at', 'DESC')->get();

        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::whereNotIn('id', Category::distinct()->pluck('parent_id')->toArray())->get();
        
        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookRequest $request)
    {
        $data = $request->all();
        $book = Book::create($data);

        $file = $data['image'];
        $image = [];
        $image['imageable_type'] = get_class($book);
        $image['imageable_id'] = $book->id;
        $image['path'] = $book->id . '_' . $file->getClientOriginalName();

        Image::create($image);
        $file->move(public_path('uploads'), $image['path']);

        return redirect()->route('books.index')->with('success', __('messages.add-book-success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::with(['category', 'image'])->findOrFail($id);

        return view('admin.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $book = Book::with(['category', 'image'])->findOrFail($id);
        $categories = Category::whereNotIn('id', Category::distinct()->pluck('parent_id')->toArray())->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, $id)
    {
        $book = Book::with('image')->findOrFail($id);
        $book->update($request->all());
        if (isset($request->image)) {
            $file = $request->image;
            $path = $id . '_' . $file->getClientOriginalName();
            Image::where('id', $book->image->id)->update(['path' => $path]);
            $file->move(public_path('uploads'), $path);
        }

        return redirect()->route('books.index')->with('success', __('messages.update-book-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        $book->image->delete();

        return redirect()->route('books.index')->with('success', __('messages.delete-book-success'));
    }
}
