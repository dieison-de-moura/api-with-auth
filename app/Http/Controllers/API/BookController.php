<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Books;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->sendResponse(Books::all(), 'Success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        try {
            $books = Books::findOrFail($id);
            return $this->sendResponse($books, 'Book retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Book {$id} not found.");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'isbn' => 'numeric',
            'value' => ['regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $books = Books::create($data);
        } catch (QueryException $e) {
            return $this->sendError('Error.', ['data' => $e->getMessage()], 400);
        }

        return $this->sendResponse($books, 'Book created successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update($id, Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'isbn' => 'numeric',
            'value' => ['regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $books = Books::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Book {$id} not found.");
        }

        if (empty($books)) {
            return $this->sendError("Book {$id} not found");
        }

        $books->name = $data['name'];
        if (!empty($data['isbn'])) {
            $books->isbn = $data['isbn'];    
        }
        if (!empty($data['value'])) {
            $books->value = $data['value'];    
        }
        $books->save();

        return $this->sendResponse($books, 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $book = Books::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->sendError("Book {$id} not found.");
        }
        $book->delete();

        return $this->sendResponse([], 'Book deleted successfully.', 204);
    }
}
