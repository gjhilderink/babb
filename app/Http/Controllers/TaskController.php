<?php

namespace App\Http\Controllers;

use App\Models\EventTask;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = Task::with(['assignedTo', 'creator', 'meeting'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->user_id, fn ($q, $u) => $q->where('assigned_to_user_id', $u))
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%$s%"))
            ->when(!$request->status, fn ($q) => $q->whereNot('status', 'gereed'))
            ->orderByRaw("FIELD(priority, 'hoog', 'normaal', 'laag')")
            ->orderBy('due_date')
            ->paginate(25)
            ->withQueryString();

        // Event tasks shown as read-only context
        $userFilter = $request->user_id
            ? User::find($request->user_id)?->name
            : null;

        $eventTasksQuery = EventTask::with('event')
            ->when(!$request->status || in_array($request->status, ['open','bezig']), fn ($q) => $q->whereIn('status', ['open','bezig']))
            ->when($request->status === 'gereed', fn ($q) => $q->where('status', 'gereed'))
            ->when($userFilter, fn ($q, $n) => $q->where('assigned_to', $n))
            ->when($request->search, fn ($q, $s) => $q->where('description', 'like', "%$s%"));

        if (!$request->status) {
            $eventTasksQuery->whereNot('status', 'gereed');
        }

        $eventTasks = $eventTasksQuery->orderBy('due_date')->get();

        // Leads assigned to someone = opvolging taak for that person
        $showGereed = $request->status === 'gereed';
        $leads = Lead::with('assignedTo')
            ->whereNotNull('assigned_to_user_id')
            ->when(!$showGereed, fn ($q) => $q->whereNotIn('status', ['gewonnen', 'verloren']))
            ->when($showGereed, fn ($q) => $q->whereIn('status', ['gewonnen', 'verloren']))
            ->when($request->user_id, fn ($q, $u) => $q->where('assigned_to_user_id', $u))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q2) =>
                $q2->where('first_name', 'like', "%$s%")
                   ->orWhere('last_name', 'like', "%$s%")
                   ->orWhere('action_required', 'like', "%$s%")
            ))
            ->orderBy('updated_at', 'desc')
            ->get();

        $users = User::orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'users', 'eventTasks', 'leads'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        return view('tasks.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTask($request);
        $data['created_by'] = auth()->id();

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Taak aangemaakt.');
    }

    public function edit(Task $task): View
    {
        $users = User::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $task->update($this->validateTask($request));

        return redirect()->route('tasks.index')->with('success', 'Taak bijgewerkt.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Taak verwijderd.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $request->validate(['status' => 'required|in:open,bezig,gereed']);
        $task->update(['status' => $request->status]);
        return back()->with('success', 'Status bijgewerkt.');
    }

    private function validateTask(Request $request): array
    {
        return $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'assigned_to_user_id' => 'required|exists:users,id',
            'due_date'           => 'nullable|date',
            'status'             => 'required|in:open,bezig,gereed',
            'priority'           => 'required|in:laag,normaal,hoog',
        ]);
    }
}
