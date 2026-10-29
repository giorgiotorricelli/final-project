<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreMovieRequest; //importo della mia request di validazione personalizzata
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;

class MoviesResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $movieList = Movie::all();

        $existingIds = [];

        // controllo per i genres
        if (isset($request['genre_ids'])) { // array associativo con gli id dei generi
            $genre_ids = $request['genre_ids'];

            foreach ($genre_ids as $genre_id) {
                if (Genre::where('id', (int) $genre_id)->exists()) {
                    $existingIds[] = $genre_id;
                }
            }

        } 

        if (isset($request['genre_ids']) && count($existingIds) > 0) {
            $movieList = Movie::whereHas('genres', function ($genresTable) use($existingIds) {
                $genresTable->whereIn('genres.id', $existingIds);
            })->with('genres')->get();
        } else {
            $movieList = Movie::with('genres')->get();
        }
        

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
    public function store(Request $request) 
    {
        $genreIdExists = true;

        // controllo per i genres
        if (isset($request['genre_ids'])) { // array associativo con gli id dei generi
            $genre_ids = $request['genre_ids'];

            foreach ($genre_ids as $genre_id) {
                $genreIdExists = Genre::where('id', (int) $genre_id)->exists();
            }


        } 

        if (!$genreIdExists) {
            return back()
                    ->withInput()
                    ->withErrors([
                        'genre_error' => "Check genre/s input values, wrong genre/s id/s"
                    ]);
        }

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
            'tmdb_id' => ['nullable', 'integer', 'max:20'],
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
        $movie = Movie::create($validated); // restituisce l'istanza del film creato

        if (isset($request['genre_ids']) && count($request['genre_ids']) > 0 ) {
            $movie->genres()->sync($request['genre_ids']);
        }
        

        return redirect()->route('admin.movies.index')->with('success', 'Film creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)->with('genres')->firstOrFail();

        return view('admin.movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $movie = Movie::where('slug', $slug)->with('genres')->firstOrFail();

        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        $genreIdExists = true;

        // controllo per i genres
        if (isset($request['genre_ids'])) { // array associativo con gli id dei generi
            $genre_ids = $request['genre_ids'];

            foreach ($genre_ids as $genre_id) {
                $genreIdExists = Genre::where('id', (int) $genre_id)->exists();
            }


        } 

        if (!$genreIdExists) {
            return back()
                    ->withInput()
                    ->withErrors([
                        'genre_error' => "Check genre/s input values, wrong genre/s id/s"
                    ]);
        }

        $slug = Str::slug($request->title . ' ' . $request->release_date);

        if ($slug !== $movie->slug) {
            if (Movie::where('slug', $slug)->exists()) {
                // Se esiste già, torniamo indietro bloccando tutto,
                // tenendo i vecchi input e stampando l'errore sul campo 'title'
                return back()
                    ->withInput()
                    ->withErrors([
                        'title' => 'There is already a movie with this title and release date.'
                    ]);
            }
        }

        $validated = $request->validate([
            'tmdb_id' => ['nullable', 'integer', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'release_date' => ['required', 'date'],
            'rating' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:500'],
            'poster_path' => ['nullable', 'image'],
            'backdrop_path' => ['nullable', 'image'],
        ]);

        $validated['slug'] = $slug;

        // dd($validated);
        

        $hasNoPoster = $request->boolean('noPosterCheck'); // se è stata spuntata non c'è poster
        $hasNoBackdrop = $request->boolean('noBackdropCheck'); // se è stata spuntata non c'è backdrop

        if (!$hasNoPoster) { // quindi: 1. c'è il poster nuovo, 2. c'è quello vecchio 3. o non c'è mai stato un poster

            // vedo se il poster nuovo non c'è
            if (!isset($validated['poster_path'])) {
                if ($movie->poster_path !== null) { // caso 2
                    // in questo caso non cancello nessun poster da public
                    $validated['poster_path'] = $movie->poster_path;
                } 
            } else { // se c'è il poster nuovo
                if ($movie->poster_path && Storage::disk('public')->exists($movie->poster_path)) {
                    Storage::disk('public')->delete($movie->poster_path); // cancello quello vecchio se esiste
                }
                $validated['poster_path'] = Storage::disk('public')->put('posters', $validated['poster_path']);
            }

            // da notare come caso 1 e 3 sono già validati dalla funzione validate() 

        } else {
            if ($movie->poster_path && Storage::disk('public')->exists($movie->poster_path)) {
                Storage::disk('public')->delete($movie->poster_path); // cancello quello vecchio se esiste
            }
            $validated['poster_path'] = null;
        }
        

        if (!$hasNoBackdrop) { // stesso controllo di prima

            if (!isset($validated['backdrop_path'])) {
                if ($movie->backdrop_path !== null) {
                    $validated['backdrop_path'] = $movie->backdrop_path;
                }
            } else { // se c'è il poster nuovo
                if ($movie->backdrop_path && Storage::disk('public')->exists($movie->backdrop_path)) {
                    Storage::disk('public')->delete($movie->backdrop_path); // cancello quello vecchio se esiste
                }
                $validated['backdrop_path'] = Storage::disk('public')->put('backdrops', $validated['backdrop_path']);
            }
        } else {
            if ($movie->backdrop_path && Storage::disk('public')->exists($movie->backdrop_path)) {
                Storage::disk('public')->delete($movie->backdrop_path); // cancello quello vecchio se esiste
            }
            $validated['backdrop_path'] = null;
        }

        //dd($validated, $hasNoPoster, $hasNoBackdrop);

        $movie->update($validated);

        if (isset($request['genre_ids']) && count($request['genre_ids']) > 0 ) {
            $movie->genres()->sync($request['genre_ids']);
        }
        return redirect()->route('admin.movies.show', $validated['slug']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $movie = Movie::where('slug', $slug)->firstOrFail();
        $movie->delete();
        return redirect()->route('admin.movies.index');
    }
}
