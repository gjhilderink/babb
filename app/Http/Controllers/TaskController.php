<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = Task::with(['assignedTo', 'creator'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->user_id, fn ($q, $u) => $q->where('assigned_to_user_id', $u))
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%$s%"))
            ->whereNot('status', 'gereed')
            ->orderByRaw("FIELD(priority, 'hoog', 'normaal', 'laag')")
            ->orderBy('due_date')
            ->paginate(25)
            ->withQueryString();

        $users = User::orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'users'));
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
