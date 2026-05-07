<?php

namespace App\Http\Controllers;

use App\Mail\DeclaratieMail;
use App\Models\Event;
use App\Models\EventCost;
use App\Models\EventTask;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::withCount('tasks')
            ->with('costs')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%$s%"))
            ->orderBy('event_date', 'asc')
            ->paginate(25)
            ->withQueryString();

        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        return view('events.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateEvent($request);

        $event = Event::create($data);

        $this->syncTasks($event, $request->input('tasks', []));
        $this->syncCosts($event, $request->input('costs', []));

        return redirect()->route('events.show', $event)->with('success', 'Evenement aangemaakt.');
    }

    public function show(Event $event): View
    {
        $event->load(['tasks', 'costs']);

        return view('events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $event->load(['tasks', 'costs']);
        $users = User::orderBy('name')->get();

        return view('events.edit', compact('event', 'users'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validateEvent($request);

        $event->update($data);

        $event->tasks()->delete();
        // Delete only costs whose receipt we are not keeping
        $event->costs()->delete();
        $this->syncTasks($event, $request->input('tasks', []));
        $this->syncCosts($event, $request->input('costs', []));

        return redirect()->route('events.show', $event)->with('success', 'Evenement bijgewerkt.');
    }

    public function uploadReceipt(Request $request, EventCost $cost): RedirectResponse
    {
        $request->validate(['receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240']);

        // Delete old file if present
        if ($cost->receipt_path && file_exists(public_path($cost->receipt_path))) {
            unlink(public_path($cost->receipt_path));
        }

        $file = $request->file('receipt');
        $dir  = 'uploads/events/receipts';
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());

        if (!is_dir(public_path($dir))) {
            mkdir(public_path($dir), 0775, true);
        }
        $file->move(public_path($dir), $name);

        $cost->update(['receipt_path' => "{$dir}/{$name}"]);

        return back()->with('success', 'Bijlage opgeslagen.');
    }

    public function deleteReceipt(EventCost $cost): RedirectResponse
    {
        if ($cost->receipt_path && file_exists(public_path($cost->receipt_path))) {
            unlink(public_path($cost->receipt_path));
        }

        $cost->update(['receipt_path' => null]);

        return back()->with('success', 'Bijlage verwijderd.');
    }

    public function mailDeclaratie(Event $event): RedirectResponse
    {
        $event->load('costs');

        $hasReceipts = $event->costs->filter(fn ($c) => $c->receipt_path)->isNotEmpty();
        if (!$hasReceipts) {
            return back()->with('error', 'Er zijn geen bijlagen om te versturen.');
        }

        Mail::to('visionair.babb@mailtobasecone.com')->send(new DeclaratieMail($event));

        return back()->with('success', 'Declaratie verstuurd naar visionair.babb@mailtobasecone.com');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Evenement verwijderd.');
    }

    public function updateTaskStatus(Request $request, EventTask $task): RedirectResponse
    {
        $request->validate(['status' => 'required|in:open,bezig,gereed']);

        $task->update(['status' => $request->status]);

        return back()->with('success', 'Taakstatus bijgewerkt.');
    }

    public function updateTaskPriority(Request $request, EventTask $task): RedirectResponse
    {
        $request->validate(['priority' => 'required|in:laag,normaal,hoog']);

        $task->update(['priority' => $request->priority]);

        return back()->with('success', 'Prioriteit bijgewerkt.');
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'event_date'    => 'required|date',
            'event_end'     => 'nullable|date|after_or_equal:event_date',
            'location'      => 'nullable|string|max:255',
            'status'        => 'required|in:concept,bevestigd,afgerond,geannuleerd',
            'max_attendees' => 'nullable|integer|min:1',
            'budget'        => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);
    }

    private function syncTasks(Event $event, array $tasks): void
    {
        foreach ($tasks as $task) {
            if (empty(trim($task['description'] ?? ''))) {
                continue;
            }
            EventTask::create([
                'event_id'    => $event->id,
                'description' => $task['description'],
                'assigned_to' => $task['assigned_to'] ?? null,
                'status'      => $task['status'] ?? 'open',
                'due_date'    => $task['due_date'] ?: null,
            ]);
        }
    }

    private function syncCosts(Event $event, array $costs): void
    {
        foreach ($costs as $cost) {
            if (empty(trim($cost['description'] ?? '')) || !is_numeric($cost['amount'] ?? '')) {
                continue;
            }
            EventCost::create([
                'event_id'     => $event->id,
                'description'  => $cost['description'],
                'amount'       => $cost['amount'],
                'category'     => $cost['category'] ?? null,
                'paid_by'      => $cost['paid_by'] ?? null,
                'paid_at'      => $cost['paid_at'] ?: null,
                'receipt_path' => $cost['receipt_path'] ?: null,
            ]);
        }
    }
}
