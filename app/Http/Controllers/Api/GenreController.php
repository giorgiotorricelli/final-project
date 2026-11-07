<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = Genre::all();

        if ($genres) {
            return response()->json([
                'successful' => true,
                'results' => $genres
            ]);
        } else {
            return response()->json([
                'successful' => false,
                'results' => null
            ], 404);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $genre = Genre::with('movies')->find($id);

        if ($genre) {
            return response()->json([
                'successful' => true,
                'results' => $genre
            ]);
        } else {
            return response()->json([
                'successful' => false,
                'results' => null
            ], 404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
