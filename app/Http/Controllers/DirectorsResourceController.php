<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Director;
use App\Models\Movie;

class DirectorsResourceController extends Controller
{
    public function index()
    {
        $directors = Director::with('movies')->get();
        return view('admin.directors.index', compact('directors'));
    }

    public function create()
    {
        // Mostra solo i film che non hanno ancora un director assegnato
        $movies = Movie::whereNull('director_id')->orderBy('title')->get();
        return view('admin.directors.create', compact('movies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => ['required', 'string'],
            "birthday" => ['required', 'date'],
            "deathday" => ['nullable', 'date', 'after:birthday'],
            "biography" => ['nullable', 'string', 'max:65535'],
            "place_of_birth" => ['required', 'string', 'max:100'],
            "profile_path" => ['nullable', 'image'],
            "movie_slugs" => ['nullable', 'array'], 
            "movie_slugs.*" => ['string', 'exists:movies,slug'], 
        ], [
            "deathday.after" => "Deathday must come after birthday :)"
        ]);

        if ($request->hasFile('profile_path')) {
            $validated['profile_path'] = Storage::disk('public')->put('directors', $request->file('profile_path'));
        }

        $director = Director::create($validated);

        // Assegna il director_id ai film selezionati
        if (!empty($validated['movie_slugs'])) {
            Movie::whereIn('slug', $validated['movie_slugs'])->update(['director_id' => $director->id]);
        }

        return redirect()->route('admin.directors.show', $director->id);
    }

    public function show(string $id)
    {
        $director = Director::where('id', $id)->with('movies')->firstOrFail();
        return view('admin.directors.show', compact('director'));
    }

    public function edit(string $id)
    {
        $director = Director::where('id', $id)->with('movies')->firstOrFail();

        // Film già associati a questo regista
        $moviesChecked = $director->movies;
        
        // Film liberi (che non hanno alcun regista o che sono di questo regista)
        $moviesUnchecked = Movie::whereNull('director_id')
            ->orWhere('director_id', $director->id)
            ->whereNotIn('id', $moviesChecked->pluck('id'))
            ->orderBy('title')
            ->get();

        return view('admin.directors.edit', compact('director', 'moviesChecked', 'moviesUnchecked'));
    }

    public function update(Request $request, Director $director)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'deathday' => ['nullable', 'date'],
            'rating' => ['nullable', 'numeric'],
            'biography' => ['nullable', 'string', 'max:65535'],
            'place_of_birth' => ['required', 'string', 'max:100'],
            'profile_path' => ['nullable', 'image'],
            'movie_slugs' => ['nullable', 'array'],
            'movie_slugs.*' => ['string', 'exists:movies,slug'],
        ]);

        $hasNoPoster = $request->boolean('noProfileCheck');

        if (!$hasNoPoster) {
            if (!isset($validated['profile_path'])) {
                if ($director->profile_path !== null) {
                    $validated['profile_path'] = $director->profile_path;
                } 
            } else {
                if ($director->profile_path && Storage::disk('public')->exists($director->profile_path)) {
                    Storage::disk('public')->delete($director->profile_path);
                }
                $validated['profile_path'] = Storage::disk('public')->put('directors', $validated['profile_path']);
            }
        } else {
            if ($director->profile_path && Storage::disk('public')->exists($director->profile_path)) {
                Storage::disk('public')->delete($director->profile_path);
            }
            $validated['profile_path'] = null;
        }

        $director->update($validated);

        // Libera tutti i film precedentemente associati a questo director e riassegna i nuovi selezionati
        Movie::where('director_id', $director->id)->update(['director_id' => null]);
        if (!empty($validated['movie_slugs'])) {
            Movie::whereIn('slug', $validated['movie_slugs'])->update(['director_id' => $director->id]);
        }

        return redirect()->route('admin.directors.show', $director->id);
    }

    public function destroy(string $id)
    {
        $director = Director::where('id', $id)->firstOrFail();

        if ($director->profile_path && Storage::disk('public')->exists($director->profile_path)) {
            Storage::disk('public')->delete($director->profile_path); 
        }

        // Sgancia i film impostando director_id a null
        $director->movies()->update(['director_id' => null]);

        $director->delete();
        return redirect()->route('admin.directors.index');
    }
}