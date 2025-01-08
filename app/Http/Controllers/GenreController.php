<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $genres = Genre::query()
                    ->when($search, fn ($query) => $query->where('name', 'ilike', "$search"))
                    ->paginate(5);

        return view('genre.index', [
            'genres' => $genres
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('genre.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request)
    {
        // validasi lewat StoreGenreRequest
        $validatedInput = $request->validated();
        try {
            Genre::create($validatedInput);
        } catch (\Exception $e) {
            Log::error("GENRE.STORE", [
                'request'   => $validatedInput,
                'message'   => $e->getMessage()
            ]);

            return redirect()->back()->withInput()
                    ->with('error', app()->isProduction() ? 'System Error!' : $e->getMessage());
        }
        return redirect()->route('genre.index')
                    ->with('success', 'New Genre has been added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Genre $genre)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Genre $genre)
    {
        return view('genre.edit', [
            'genre' => $genre
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $validatedInput = $request->validated();
        try {
            $genre->update($validatedInput);
        } catch (\Exception $e) {
            Log::error("GENRE.UPDATE", [
                'request'   => $validatedInput,
                'message'   => $e->getMessage()
            ]);

            return redirect()->back()->withInput()
                    ->with('error', app()->isProduction() ? 'System Error!' : $e->getMessage());
        }
        return redirect()->route('genre.index')
                    ->with('success', 'Genre has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre)
    {
        try {
            $genre->delete();
        } catch (\Exception $e) {
            Log::error("GENRE.DESTROY", [
                'request'   => $genre,
                'message'   => $e->getMessage()
            ]);

            return redirect()->back()->withInput()
                    ->with('error', app()->isProduction() ? 'System Error!' : $e->getMessage());
        }
        return redirect()->route('genre.index')
                    ->with('success', 'Genre has been deleted!');
    }
}
