<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class GenresResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = Genre::all();

        return view('admin.genres.index', compact('genres'));
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
        ]);

        $validated['name'] = ucfirst($validated['name']); // ucfirst capitalizza la prima lettera
        $nameExists = Genre::where('name', $validated['name'])->exists();

        if ($nameExists) {
            return back()
                    ->withInput()
                    ->withErrors([
                        'name' => 'There is already a genre with this name.'
                    ]);
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $validated['name'])) { // controlla che ci siano solo caratteri alfabetici e spazi
            return back()
                    ->withInput()
                    ->withErrors([
                        'name' => 'The name must contain only alphabetical characters and spaces.'
                    ]);
        }

        $newId = Genre::max('id') + 1;

        $validated['id'] = $newId;

        Genre::create($validated);

        return redirect()->route('admin.genres.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // personalizedKey è la stringa con la quale personalizzo il nome dell'errore
        $personalizedKey = 'name_' . (string) $id;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
        ]);

        $validated['name'] = ucfirst($validated['name']); // ucfirst capitalizza la prima lettera

        if (!preg_match('/^[a-zA-Z\s]+$/', $validated['name'])) { // controlla che ci siano solo caratteri alfabetici e spazi
            return back()
                    ->withInput()
                    ->withErrors([
                        $personalizedKey => 'Allowed: a-z A-Z and spaces only.'
                    ]);
        }

        $genre = Genre::where('id', $id)->firstOrFail();

        
        $exists = Genre::where('name', $validated['name'])->exists();

        if($exists) {
            return back()
                    ->withInput()
                    ->withErrors([
                        $personalizedKey => "Name already taken.",
                    ]);
        }
        

        $genre->name = $validated['name'];
        $genre->save();
        return redirect()->route('admin.genres.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        dd($id);
    }
}
