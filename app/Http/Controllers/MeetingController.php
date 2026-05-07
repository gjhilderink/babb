<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingNote;
use App\Models\Task;
use App\Models\User;
use App\Services\AclService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(AclService::allowed('meetings.view'), 403);

        $meetings = Meeting::with('creator')
            ->withCount('notes')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%$s%"))
            ->orderBy('meeting_date', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('meetings.index', compact('meetings'));
    }

    public function create(): View
    {
        abort_unless(AclService::allowed('meetings.manage'), 403);
        return view('meetings.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(AclService::allowed('meetings.manage'), 403);
        $data = $this->validateMeeting($request);
        $data['created_by'] = auth()->id();

        $meeting = Meeting::create($data);

        return redirect()->route('meetings.show', $meeting)->with('success', 'Vergadering aangemaakt.');
    }

    public function show(Meeting $meeting): View
    {
        abort_unless(AclService::allowed('meetings.view'), 403);
        $meeting->load(['notes.user', 'creator', 'tasks.assignedTo']);

        $users = User::orderBy('name')->get();

        return view('meetings.show', compact('meeting', 'users'));
    }

    public function edit(Meeting $meeting): View
    {
        abort_unless(AclService::allowed('meetings.manage'), 403);
        return view('meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting): RedirectResponse
    {
        abort_unless(AclService::allowed('meetings.manage'), 403);
        $meeting->update($this->validateMeeting($request));

        return redirect()->route('meetings.show', $meeting)->with('success', 'Vergadering bijgewerkt.');
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        abort_unless(AclService::allowed('meetings.manage'), 403);
        $meeting->delete();

        return redirect()->route('meetings.index')->with('success', 'Vergadering verwijderd.');
    }

    public function storeTask(Request $request, Meeting $meeting): RedirectResponse
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'assigned_to_user_id' => 'required|exists:users,id',
            'due_date'           => 'nullable|date',
            'priority'           => 'required|in:laag,normaal,hoog',
        ]);

        Task::create([
            'title'              => $data['title'],
            'assigned_to_user_id' => $data['assigned_to_user_id'],
            'due_date'           => $data['due_date'] ?: null,
            'priority'           => $data['priority'],
            'meeting_id'         => $meeting->id,
            'created_by'         => auth()->id(),
            'status'             => 'open',
        ]);

        return back()->with('success', 'Taak aangemaakt en gekoppeld aan vergadering.');
    }

    public function saveNote(Request $request, Meeting $meeting): RedirectResponse
    {
        $request->validate(['content' => 'required|string']);

        MeetingNote::updateOrCreate(
            ['meeting_id' => $meeting->id, 'user_id' => auth()->id()],
            ['content' => $request->content]
        );

        return back()->with('success', 'Notitie opgeslagen.');
    }

    public function deleteNote(Meeting $meeting): RedirectResponse
    {
        MeetingNote::where('meeting_id', $meeting->id)
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('success', 'Notitie verwijderd.');
    }

    private function validateMeeting(Request $request): array
    {
        return $request->validate([
            'title'        => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'location'     => 'nullable|string|max:255',
            'agenda'       => 'nullable|string',
            'status'       => 'required|in:gepland,afgerond,geannuleerd',
        ]);
    }
}
