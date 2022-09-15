<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreChirpRequest;
use Inertia\Response as InertiaResponse;
use App\Http\Requests\UpdateChirpRequest;

class ChirpController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Chirp::class, 'chirp');
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('Chirps/Index', [
            'chirps' => Chirp::with('user:id,name')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(StoreChirpRequest $request): RedirectResponse
    {
        $request->user()->chirps()->create($request->validated());

        return redirect(route('chirps.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function edit(Chirp $chirp)
    {
        //
    }

    public function update(UpdateChirpRequest $request, Chirp $chirp): RedirectResponse
    {
        $chirp->update($request->validated());

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chirp $chirp)
    {
        //
    }
}
