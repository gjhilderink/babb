<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::with('membershipType')
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('company_name', 'like', "%$s%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->membership_type_id, fn ($q, $id) => $q->where('membership_type_id', $id))
            ->orderBy('last_name')
            ->paginate(50)
            ->withQueryString();

        $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        return view('members.index', compact('members', 'membershipTypes'));
    }

    public function create(): View
    {
        $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        return view('members.create', compact('membershipTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'              => 'required|email|unique:members,email',
            'phone'              => 'nullable|string|max:30',
            'company_name'       => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:255',
            'postal_code'        => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:255',
            'country'            => 'nullable|string|size:2',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'membership_start'   => 'nullable|date',
            'membership_end'     => 'nullable|date|after_or_equal:membership_start',
            'status'             => 'required|in:active,inactive,suspended',
            'notes'              => 'nullable|string',
        ]);

        Member::create($data);

        return redirect()->route('members.index')->with('success', 'Lid aangemaakt.');
    }

    public function show(Member $member): View
    {
        $member->load(['membershipType', 'invoices' => fn ($q) => $q->latest()->limit(10)]);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        return view('members.edit', compact('member', 'membershipTypes'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $request->validate([
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'              => 'required|email|unique:members,email,'.$member->id,
            'phone'              => 'nullable|string|max:30',
            'company_name'       => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:255',
            'postal_code'        => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:255',
            'country'            => 'nullable|string|size:2',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'membership_start'   => 'nullable|date',
            'membership_end'     => 'nullable|date|after_or_equal:membership_start',
            'status'             => 'required|in:active,inactive,suspended',
            'notes'              => 'nullable|string',
        ]);

        $member->update($data);

        return redirect()->route('members.show', $member)->with('success', 'Lid bijgewerkt.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Lid verwijderd.');
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="leden-' . now()->format('Y-m-d') . '.csv"',
        ];

        $columns = [
            'first_name', 'last_name', 'email', 'phone', 'company_name',
            'address', 'postal_code', 'city', 'country',
            'membership_type', 'membership_start', 'membership_end', 'status', 'notes',
        ];

        return response()->stream(function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($handle, $columns, ';');

            Member::with('membershipType')->orderBy('last_name')->chunk(200, function ($members) use ($handle) {
                foreach ($members as $m) {
                    fputcsv($handle, [
                        $m->first_name,
                        $m->last_name,
                        $m->email,
                        $m->phone,
                        $m->company_name,
                        $m->address,
                        $m->postal_code,
                        $m->city,
                        $m->country,
                        $m->membershipType?->name,
                        $m->membership_start?->format('d-m-Y'),
                        $m->membership_end?->format('d-m-Y'),
                        $m->status,
                        $m->notes,
                    ], ';');
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Strip optional BOM
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }

        $header = fgetcsv($handle, 0, ';');
        if (!$header) {
            return back()->with('error', 'Ongeldig CSV-bestand.');
        }

        // Normalize header keys
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $membershipTypes = MembershipType::pluck('id', 'name');
        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) !== count($header)) continue;
            $data = array_combine($header, $row);

            $email = trim($data['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $skipped++; continue; }

            $typeId = null;
            if (!empty($data['membership_type'])) {
                $typeId = $membershipTypes[$data['membership_type']] ?? null;
            }

            $parseDate = function (?string $v): ?string {
                if (!$v) return null;
                // Accept d-m-Y or Y-m-d
                foreach (['d-m-Y', 'Y-m-d'] as $fmt) {
                    $d = \DateTime::createFromFormat($fmt, trim($v));
                    if ($d) return $d->format('Y-m-d');
                }
                return null;
            };

            $status = in_array(trim($data['status'] ?? ''), ['active', 'inactive', 'suspended'])
                ? trim($data['status'])
                : 'active';

            Member::updateOrCreate(
                ['email' => $email],
                [
                    'first_name'         => trim($data['first_name']   ?? ''),
                    'last_name'          => trim($data['last_name']    ?? ''),
                    'phone'              => trim($data['phone']        ?? '') ?: null,
                    'company_name'       => trim($data['company_name'] ?? '') ?: null,
                    'address'            => trim($data['address']      ?? '') ?: null,
                    'postal_code'        => trim($data['postal_code']  ?? '') ?: null,
                    'city'               => trim($data['city']         ?? '') ?: null,
                    'country'            => strtoupper(trim($data['country'] ?? '')) ?: null,
                    'membership_type_id' => $typeId,
                    'membership_start'   => $parseDate($data['membership_start'] ?? null),
                    'membership_end'     => $parseDate($data['membership_end']   ?? null),
                    'status'             => $status,
                    'notes'              => trim($data['notes'] ?? '') ?: null,
                ]
            );
            $imported++;
        }

        fclose($handle);

        return back()->with('success', "{$imported} leden geimporteerd" . ($skipped ? ", {$skipped} overgeslagen (ongeldig e-mailadres)." : '.'));
    }
}
