<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreMovieRequest; //importo della mia request di validazione personalizzata
use Illuminate\Http\Request;
use App\Models\Movie;

class MoviesResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movieList = Movie::all();

        return view('admin.movies.index', compact('movieList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    { 
        return view('admin.movies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) //sostituisco la generica Request con la mia request personalizzata
    {
        $slug = Str::slug($request->title . ' ' . $request->release_date);

        
        // 2. CONTROLLO MANUALE DEL DOPPIONE NEL DB
        if (Movie::where('slug', $slug)->exists()) {
            // Se esiste già, torniamo indietro bloccando tutto, 
            // tenendo i vecchi input e stampando l'errore sul campo 'title'
            return back()
                ->withInput()
                ->withErrors([
                    'title' => 'Esiste già un film registrato con questo titolo e questa data di rilascio.'
                ]);
        }

        // 3. Validiamo gli altri campi standard
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'release_date' => ['required', 'date'],
            'rating' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:500'],
            'poster_path' => ['nullable', 'image'],
            'backdrop_path' => ['nullable', 'image'],
        ]);

        // 4. Aggiungiamo lo slug ai dati validati
        $validated['slug'] = $slug;

        if (isset($validated['poster_path'])) {
            $validated['poster_path'] = Storage::disk('public')->put('posters', $validated['poster_path']);
        }
        
        if (isset($validated['backdrop_path'])) {
            $validated['backdrop_path'] = Storage::disk('public')->put('backdrops', $validated['backdrop_path']);
        }

        // 5. Salviamo nel database
        Movie::create($validated);

        return redirect()->route('admin.movies.index')->with('success', 'Film creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)->firstOrFail();

        return view('admin.movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $movie = Movie::where('slug', $slug)->firstOrFail();

        return view('admin.movies.edit', compact('movie'));
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
    public function destroy(Movie $movie)
    {
        $movie->delete();
        return redirect()->route('admin.movies.index');
    }
}
