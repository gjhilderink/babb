<?php

namespace App\Http\Controllers;

use App\Models\Declaratie;
use App\Support\Seizoen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DeclaratiesController extends Controller
{
    public function index(Request $request): View
    {
        // "Jaar" = seizoen (1 sep t/m 31 aug), aangeduid met het startjaar
        $jaar    = $request->integer('jaar', Seizoen::huidig());
        $periode = Seizoen::periode($jaar);

        $declaraties = Declaratie::with('creator')
            ->whereBetween('datum', $periode)
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('omschrijving', 'like', "%$s%"))
            ->orderBy('datum', 'desc')
            ->paginate(25)
            ->withQueryString();

        $totaalJaar = Declaratie::whereBetween('datum', $periode)->sum('bedrag');
        $totaalBetaald = Declaratie::whereBetween('datum', $periode)->where('status', 'betaald')->sum('bedrag');
        $totaalOpen = Declaratie::whereBetween('datum', $periode)->whereIn('status', ['ingediend', 'goedgekeurd'])->sum('bedrag');

        $beschikbareJaren = Declaratie::selectRaw(Seizoen::sql('datum').' as jaar')
            ->groupBy('jaar')
            ->orderByDesc('jaar')
            ->pluck('jaar')
            ->map(fn ($j) => (int) $j);

        if ($beschikbareJaren->isEmpty()) {
            $beschikbareJaren = collect([Seizoen::huidig()]);
        }

        return view('declaraties.index', compact(
            'declaraties', 'jaar', 'totaalJaar', 'totaalBetaald', 'totaalOpen', 'beschikbareJaren'
        ));
    }

    public function create(): View
    {
        return view('declaraties.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateDeclaratie($request);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('bijlage')) {
            $file = $request->file('bijlage');
            $data['bijlage_path'] = $file->store('declaraties', 'public');
            $data['bijlage_naam'] = $file->getClientOriginalName();
        }

        Declaratie::create($data);

        return redirect()->route('declaraties.index')->with('success', 'Declaratie aangemaakt.');
    }

    public function edit(Declaratie $declaratie): View
    {
        return view('declaraties.edit', compact('declaratie'));
    }

    public function update(Request $request, Declaratie $declaratie): RedirectResponse
    {
        $data = $this->validateDeclaratie($request);

        if ($request->hasFile('bijlage')) {
            if ($declaratie->bijlage_path) {
                Storage::disk('public')->delete($declaratie->bijlage_path);
            }
            $file = $request->file('bijlage');
            $data['bijlage_path'] = $file->store('declaraties', 'public');
            $data['bijlage_naam'] = $file->getClientOriginalName();
        }

        $declaratie->update($data);

        return redirect()->route('declaraties.index')->with('success', 'Declaratie bijgewerkt.');
    }

    public function destroy(Declaratie $declaratie): RedirectResponse
    {
        if ($declaratie->bijlage_path) {
            Storage::disk('public')->delete($declaratie->bijlage_path);
        }

        $declaratie->delete();

        return redirect()->route('declaraties.index')->with('success', 'Declaratie verwijderd.');
    }

    public function updateStatus(Request $request, Declaratie $declaratie): RedirectResponse
    {
        $request->validate(['status' => 'required|in:ingediend,goedgekeurd,betaald,afgewezen']);
        $declaratie->update(['status' => $request->status]);

        return back()->with('success', 'Status bijgewerkt.');
    }

    public function download(Declaratie $declaratie)
    {
        abort_unless($declaratie->hasBijlage(), 404);

        return Storage::disk('public')->download(
            $declaratie->bijlage_path,
            $declaratie->bijlage_naam
        );
    }

    private function validateDeclaratie(Request $request): array
    {
        return $request->validate([
            'omschrijving' => 'required|string|max:255',
            'bedrag'       => 'required|numeric|min:0',
            'datum'        => 'required|date',
            'categorie'    => 'nullable|string|max:100',
            'status'       => 'required|in:ingediend,goedgekeurd,betaald,afgewezen',
            'notities'     => 'nullable|string',
            'bijlage'      => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
        ]);
    }
}
