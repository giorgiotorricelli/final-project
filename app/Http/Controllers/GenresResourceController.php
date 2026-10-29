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
            'color' => ['required', 'string', 'max:7'],
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

        if (!preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $request['color'])) {
            return back()
                    ->withInput()
                    ->withErrors([
                        'color' => 'Non valid hex color.'
                    ]);
        }

        $newId = Genre::max('id') + 1;

        $validated['id'] = $newId;

        //dd($validated);

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
        $personalizedNameKey = 'name_' . (string) $id;
        $personalizedColorKey = 'color_' . (string) $id;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $validated['name'] = ucfirst($validated['name']); // ucfirst capitalizza la prima lettera

        if (!preg_match('/^[a-zA-Z\s]+$/', $validated['name'])) { // controlla che ci siano solo caratteri alfabetici e spazi
            return back()
                    ->withInput()
                    ->withErrors([
                        $personalizedNameKey => 'Allowed: a-z A-Z and spaces only.'
                    ]);
        }

        if (!preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $request['color'])) {
            return back()
                    ->withInput()
                    ->withErrors([
                        $personalizedColorKey => 'Non valid hex color.'
                    ]);
        }

        $genre = Genre::where('id', $id)->firstOrFail();

        
        $exists = Genre::where('name', $validated['name'])->exists();

        if($exists && $validated['name'] !== $genre->name) {
            return back()
                    ->withInput()
                    ->withErrors([
                        $personalizedNameKey => "Name already taken.",
                    ]);
        }

        $genre->name = $validated['name'];
        $genre->color = $validated['color'];
        $genre->save();
        return redirect()->route('admin.genres.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $genre = Genre::where('id', $id)->firstOrFail();
        $genre->delete();
        return redirect()->route('admin.genres.index'); 
           
    }
}
