<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Actor;
use App\Models\Movie;

class ActorsResourceController extends Controller
{
    public function index()
    {
        $actors = Actor::with('movies')->get();
        return view('admin.actors.index', compact('actors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.actors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    
    $validated = $request->validate([
        "name" => ['required', 'string'],
        "birthday" => ['required','date'],
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
        $validated['profile_path'] = Storage::disk('public')->put('actors', $request->file('profile_path'));
    }

    
    $actor = Actor::create($validated);

    
    if (!empty($validated['movie_slugs'])) {
        
        $movieIds = Movie::whereIn('slug', $validated['movie_slugs'])->pluck('id')->toArray();
        
        $actor->movies()->sync($movieIds);
    }

    return redirect()->route('admin.actors.show', $actor->id);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $actor = Actor::where('id', $id)->with('movies')->firstOrFail();
        return view('admin.actors.show', compact('actor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $actor = Actor::where('id', $id)->with('movies')->firstOrFail();

        return view('admin.actors.edit', compact('actor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Actor $actor)
    {
        

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'deathday' => ['nullable', 'date'],
            'rating' => ['nullable', 'numeric'],
            'biography' => ['nullable', 'string', 'max:65535'],
            'profile_path' => ['nullable', 'image'],
        ]);

        $hasNoPoster = $request->boolean('noProfileCheck'); // se è stata spuntata non c'è poster

        if (!$hasNoPoster) { // quindi: 1. c'è il poster nuovo, 2. c'è quello vecchio 3. o non c'è mai stato un poster

            // vedo se il poster nuovo non c'è
            if (!isset($validated['profile_path'])) {
                if ($actor->profile_path !== null) { // caso 2
                    // in questo caso non cancello nessun poster da public
                    $validated['profile_path'] = $actor->profile_path;
                } 
            } else { // se c'è il poster nuovo
                if ($actor->profile_path && Storage::disk('public')->exists($actor->profile_path)) {
                    Storage::disk('public')->delete($actor->profile_path); // cancello quello vecchio se esiste
                }
                $validated['profile_path'] = Storage::disk('public')->put('actors', $validated['profile_path']);
            }

            // da notare come caso 1 e 3 sono già validati dalla funzione validate() 

        } else {
            if ($actor->profile_path && Storage::disk('public')->exists($actor->profile_path)) {
                Storage::disk('public')->delete($actor->profile_path); // cancello quello vecchio se esiste
            }
            $validated['profile_path'] = null;
        }

        $actor->update($validated);

        if (!empty($validated['movie_slugs'])) {
        
            $movieIds = Movie::whereIn('slug', $validated['movie_slugs'])->pluck('id')->toArray();
        
            $actor->movies()->sync($movieIds);
        }
        return redirect()->route('admin.actors.show', $actor->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $actor = Actor::where('id', $id)->firstOrFail();

        if ($actor->profile_path && Storage::disk('public')->exists($actor->profile_path)) {
            Storage::disk('public')->delete($actor->profile_path); 
        }
        $actor->delete();
        return redirect()->route('admin.actors.index');
    }
}
