<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Actor;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $actors = Actor::paginate(10);

        if ($actors) {
            return response()->json([
                'successful' => true,
                'results' => $actors
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
        $actor = Actor::with('movies')->find($id);

        if ($actor) {
            return response()->json([
                'successful' => true,
                'results' => $actor
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
