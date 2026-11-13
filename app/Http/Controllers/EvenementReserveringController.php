<?php

namespace App\Http\Controllers;

use App\Models\EvenementReservering;
use App\Services\LidmaatschapAfdrachtService;
use App\Support\Seizoen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EvenementReserveringController extends Controller
{
    public function index(LidmaatschapAfdrachtService $service): View
    {
        $reserveringen = EvenementReservering::orderByDesc('jaar')->get()->map(function (EvenementReservering $r) use ($service) {
            $r->aantal_leden = $service->contributiesVoorJaar($r->jaar)['aantal'];
            $r->totaal       = $r->aantal_leden * (float) $r->bedrag_per_lid;

            return $r;
        });

        return view('evenement_reserveringen.index', compact('reserveringen'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->valideer($request);

        EvenementReservering::create($data);

        return back()->with('success', "Evenementenreservering voor seizoen ".Seizoen::label((int) $data['jaar'])." toegevoegd.");
    }

    public function update(Request $request, EvenementReservering $evenementReservering): RedirectResponse
    {
        $evenementReservering->update($this->valideer($request, $evenementReservering));

        return back()->with('success', "Evenementenreservering voor seizoen ".Seizoen::label($evenementReservering->jaar)." bijgewerkt.");
    }

    public function destroy(EvenementReservering $evenementReservering): RedirectResponse
    {
        $evenementReservering->delete();

        return back()->with('success', "Evenementenreservering voor seizoen ".Seizoen::label($evenementReservering->jaar)." verwijderd.");
    }

    private function valideer(Request $request, ?EvenementReservering $reservering = null): array
    {
        return $request->validate([
            'jaar'           => ['required', 'integer', 'min:2000', 'max:2100', Rule::unique('evenement_reserveringen', 'jaar')->ignore($reservering)],
            'bedrag_per_lid' => 'required|numeric|min:0',
            'omschrijving'   => 'nullable|string|max:255',
        ], [
            'jaar.unique' => 'Voor dit jaar bestaat al een evenementenreservering. Pas die aan in de lijst.',
        ]);
    }
}
