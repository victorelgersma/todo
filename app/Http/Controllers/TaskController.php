<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, TodoList $list): RedirectResponse
    {
        $this->authorizeList($request, $list);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $nextPosition = ($list->tasks()->max('position') ?? -1) + 1;

        $list->tasks()->create([
            'title' => $validated['title'],
            'position' => $nextPosition,
        ]);

        return redirect()->route('lists.show', $list);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($request, $task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $task->update($validated);

        return redirect()->route('lists.show', $task->list);
    }

    public function toggle(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($request, $task);

        $task->update([
            'completed_at' => $task->isComplete() ? null : now(),
        ]);

        return redirect()->route('lists.show', $task->list);
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($request, $task);

        $list = $task->list;
        $task->delete();

        return redirect()->route('lists.show', $list);
    }

    protected function authorizeList(Request $request, TodoList $list): void
    {
        abort_unless($list->user_id === $request->user()->id, 404);
    }

    protected function authorizeTask(Request $request, Task $task): void
    {
        abort_unless($task->list->user_id === $request->user()->id, 404);
    }
}
