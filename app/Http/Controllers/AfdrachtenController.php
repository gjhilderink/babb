<?php

namespace App\Http\Controllers;

use App\Models\Afdracht;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AfdrachtenController extends Controller
{
    public function index(Request $request): View
    {
        $afdrachten = Afdracht::with('creator')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('onderwerp', 'like', "%$s%"))
            ->orderBy('datum', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        $totaalBetaald    = Afdracht::where('status', 'betaald')->sum('bedrag');
        $totaalOpenstaand = Afdracht::where('status', 'nog_te_betalen')->sum('bedrag');

        return view('afdrachten.index', compact('afdrachten', 'totaalBetaald', 'totaalOpenstaand'));
    }

    public function create(): View
    {
        return view('afdrachten.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAfdracht($request);
        $data['created_by'] = auth()->id();

        Afdracht::create($data);

        return redirect()->route('afdrachten.index')->with('success', 'Afdracht aangemaakt.');
    }

    public function edit(Afdracht $afdracht): View
    {
        return view('afdrachten.edit', compact('afdracht'));
    }

    public function update(Request $request, Afdracht $afdracht): RedirectResponse
    {
        $afdracht->update($this->validateAfdracht($request));

        return redirect()->route('afdrachten.index')->with('success', 'Afdracht bijgewerkt.');
    }

    public function destroy(Afdracht $afdracht): RedirectResponse
    {
        $afdracht->delete();

        return redirect()->route('afdrachten.index')->with('success', 'Afdracht verwijderd.');
    }

    public function updateStatus(Request $request, Afdracht $afdracht): RedirectResponse
    {
        $request->validate(['status' => 'required|in:nieuw,nog_te_betalen,betaald']);
        $afdracht->update(['status' => $request->status]);

        return back()->with('success', 'Status bijgewerkt.');
    }

    private function validateAfdracht(Request $request): array
    {
        return $request->validate([
            'onderwerp' => 'required|string|max:255',
            'bedrag'    => 'required|numeric|min:0',
            'status'    => 'required|in:nieuw,nog_te_betalen,betaald',
            'datum'     => 'nullable|date',
            'notities'  => 'nullable|string',
        ]);
    }
}
