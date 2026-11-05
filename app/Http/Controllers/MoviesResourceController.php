<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreMovieRequest; 
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;

class MoviesResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $existingIds = [];

        if (isset($request['genre_ids'])) {
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
            })->with('genres', 'actors', 'director')->get();
        } else {
            $movieList = Movie::with('genres', 'actors', 'director')->get();
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

        $slug = Str::slug($request->title . ' ' . $request->release_date);

        if (Movie::where('slug', $slug)->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'title' => 'Esiste già un film registrato con questo titolo e questa data di rilascio.'
                ]);
        }

        $validated = $request->validate([
            'tmdb_id' => ['nullable', 'integer', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'director_id' => ['required', 'exists:directors,id'],
            'release_date' => ['required', 'date'],
            'rating' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:500'],
            'poster_path' => ['nullable', 'image'],
            'backdrop_path' => ['nullable', 'image'],
            'genre_ids' => ['nullable', 'array'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
            'actor_ids' => ['nullable', 'array'],
            'actor_ids.*' => ['integer', 'exists:actors,id'],
        ], [
            'director_id.required' => 'You have to select a director.',
            'director_id.exists' => 'Director id is invalid.',
        ]);

        unset($validated['genre_ids']);
        unset($validated['actor_ids']);
        $validated['slug'] = $slug;

        if (isset($validated['poster_path'])) {
            $validated['poster_path'] = Storage::disk('public')->put('posters', $validated['poster_path']);
        }
        
        if (isset($validated['backdrop_path'])) {
            $validated['backdrop_path'] = Storage::disk('public')->put('backdrops', $validated['backdrop_path']);
        }
        
        $movie = Movie::create($validated);

        if (isset($request['genre_ids']) && count($request['genre_ids']) > 0 ) {
            $movie->genres()->sync($request['genre_ids']);
        }

        if (isset($request['actor_ids']) && count($request['actor_ids']) > 0 ) {
            $movie->actors()->sync($request['actor_ids']);
        } 
        
        return redirect()->route('admin.movies.index')->with('success', 'Film creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)->with('genres', 'actors', 'director')->firstOrFail();
        return view('admin.movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $movie = Movie::where('slug', $slug)->with('genres', 'actors', 'director')->firstOrFail();
        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        $genreIdExists = true;

        if (isset($request['genre_ids'])) {
            $genre_ids = $request['genre_ids'];
            foreach ($genre_ids as $genre_id) {
                $genreIdExists = Genre::where('id', (int) $genre_id)->exists();

                if (!$genreIdExists) {
                    return back()
                            ->withInput()
                            ->withErrors([
                                'genre_error' => "Check genre/s input values, wrong genre/s id/s"
                            ]);
                }
            }
        } 

        $slug = Str::slug($request->title . ' ' . $request->release_date);

        if ($slug !== $movie->slug) {
            if (Movie::where('slug', $slug)->exists()) {
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
            'director_id' => ['required', 'exists:directors,id'],
            'release_date' => ['required', 'date'],
            'rating' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:500'],
            'poster_path' => ['nullable', 'image'],
            'backdrop_path' => ['nullable', 'image'],
            "actor_ids" => ['nullable', 'array'], 
            "actor_ids.*" => ['string', 'exists:actors,id'],
        ], [
            'director_id.required' => 'You have to select a director.',
            'director_id.exists' => 'Director id is invalid.',
        ]);


        unset($validated['actor_ids']); //li rimuovo perchè devo usare sync e update andrebbe in errore
        $validated['slug'] = $slug;

        $hasNoPoster = $request->boolean('noPosterCheck');
        $hasNoBackdrop = $request->boolean('noBackdropCheck');

        if (!$hasNoPoster) {
            if (!isset($validated['poster_path'])) {
                if ($movie->poster_path !== null) {
                    $validated['poster_path'] = $movie->poster_path;
                } 
            } else {
                if ($movie->poster_path && Storage::disk('public')->exists($movie->poster_path)) {
                    Storage::disk('public')->delete($movie->poster_path);
                }
                $validated['poster_path'] = Storage::disk('public')->put('posters', $validated['poster_path']);
            }
        } else {
            if ($movie->poster_path && Storage::disk('public')->exists($movie->poster_path)) {
                Storage::disk('public')->delete($movie->poster_path);
            }
            $validated['poster_path'] = null;
        }
        
        if (!$hasNoBackdrop) {
            if (!isset($validated['backdrop_path'])) {
                if ($movie->backdrop_path !== null) {
                    $validated['backdrop_path'] = $movie->backdrop_path;
                }
            } else {
                if ($movie->backdrop_path && Storage::disk('public')->exists($movie->backdrop_path)) {
                    Storage::disk('public')->delete($movie->backdrop_path);
                }
                $validated['backdrop_path'] = Storage::disk('public')->put('backdrops', $validated['backdrop_path']);
            }
        } else {
            if ($movie->backdrop_path && Storage::disk('public')->exists($movie->backdrop_path)) {
                Storage::disk('public')->delete($movie->backdrop_path);
            }
            $validated['backdrop_path'] = null;
        }

        $movie->update($validated);


        if (isset($request['genre_ids']) && count($request['genre_ids']) > 0 ) {
            $movie->genres()->sync($request['genre_ids']);
        }

        if (isset($request['actor_ids']) && count($request['actor_ids']) > 0 ) {
            $movie->actors()->sync($request['actor_ids']);
        }
        return redirect()->route('admin.movies.show', $validated['slug']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $movie = Movie::where('slug', $slug)->firstOrFail();

        if ($movie->poster_path && Storage::disk('public')->exists($movie->poster_path)) {
            Storage::disk('public')->delete($movie->poster_path);
        }
        
        $movie->delete();
        return redirect()->route('admin.movies.index');
    }
}