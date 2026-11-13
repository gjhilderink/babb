<?php

namespace App\Http\Controllers;

use App\Models\Reservering;
use App\Services\LidmaatschapAfdrachtService;
use App\Support\Seizoen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReserveringController extends Controller
{
    public function index(LidmaatschapAfdrachtService $service): View
    {
        $reserveringen = Reservering::orderByDesc('jaar')->get();

        // Bruto afdracht Bonboys per jaar, om te tonen wat er na reservering overblijft
        $afdrachtBonboys = $service->overzichtPerJaar('bonboys')->keyBy('jaar');

        return view('reserveringen.index', compact('reserveringen', 'afdrachtBonboys'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->valideer($request);

        Reservering::create($data);

        return back()->with('success', "Reservering voor seizoen ".Seizoen::label((int) $data['jaar'])." toegevoegd.");
    }

    public function update(Request $request, Reservering $reservering): RedirectResponse
    {
        $data = $this->valideer($request, $reservering);

        $reservering->update($data);

        return back()->with('success', "Reservering voor seizoen ".Seizoen::label($reservering->jaar)." bijgewerkt.");
    }

    public function destroy(Reservering $reservering): RedirectResponse
    {
        $reservering->delete();

        return back()->with('success', "Reservering voor seizoen ".Seizoen::label($reservering->jaar)." verwijderd.");
    }

    private function valideer(Request $request, ?Reservering $reservering = null): array
    {
        return $request->validate([
            'jaar'         => ['required', 'integer', 'min:2000', 'max:2100', Rule::unique('reserveringen', 'jaar')->ignore($reservering)],
            'bedrag'       => 'required|numeric|min:0',
            'omschrijving' => 'nullable|string|max:255',
        ], [
            'jaar.unique' => 'Voor dit jaar bestaat al een reservering. Pas die aan in de lijst.',
        ]);
    }
}
