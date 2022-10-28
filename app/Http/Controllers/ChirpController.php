<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChirpRequest;
use App\Http\Requests\UpdateChirpRequest;
use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ChirpController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Chirp::class, 'chirp', [
            'except' => ['show', 'create', 'edit'],
        ]);
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('Chirps/Index', [
            'chirps' => Chirp::with('user:id,name')->latest()->paginate(5),
        ]);
    }

    public function store(StoreChirpRequest $request): RedirectResponse
    {
        $request->user()->chirps()->create($request->validated());

        return redirect(route('chirps.index'));
    }

    public function update(UpdateChirpRequest $request, Chirp $chirp): RedirectResponse
    {
        $chirp->update($request->validated());

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): RedirectResponse
    {
        $chirp->delete();

        return redirect(route('chirps.index'));
    }
}
