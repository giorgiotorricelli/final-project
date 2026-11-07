<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Director;

class DirectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $directors = Director::paginate(10);

        if ($directors) {
            return response()->json([
                'successful' => true,
                'results' => $directors
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
        $director = Director::with('movies')->find($id);

        if ($director) {
            return response()->json([
                'successful' => true,
                'results' => $director
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
