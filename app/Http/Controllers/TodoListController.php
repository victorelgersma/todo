<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoListController extends Controller
{
    /**
     * No list selected — send them to their first list, or straight to
     * the empty state if they don't have one yet.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $first = $request->user()->todoLists()->orderBy('position')->first();

        if ($first) {
            return redirect()->route('lists.show', $first);
        }

        return view('lists.empty');
    }

    /**
     * Every list at a glance, most recently active first. "Active" means the
     * newest task added; lists without tasks fall back to their own creation
     * date so a freshly created list still surfaces near the top.
     */
    public function overview(Request $request): View
    {
        $lists = $request->user()->todoLists()
            ->withCount(['openTasks', 'completedTasks'])
            ->withMax('tasks as last_task_added_at', 'created_at')
            ->withCasts(['last_task_added_at' => 'datetime'])
            ->with(['openTasks' => fn ($query) => $query->limit(3)])
            ->get()
            ->sortByDesc(fn (TodoList $list) => ($list->last_task_added_at ?? $list->created_at)->getTimestamp())
            ->values();

        return view('lists.overview', [
            'projects' => $lists,
        ]);
    }

    public function show(Request $request, TodoList $list): View
    {
        $this->authorizeList($request, $list);

        return view('lists.show', [
            'list' => $list,
            'openTasks' => $list->openTasks()->get(),
            'completedTasks' => $list->completedTasks()->latest('completed_at')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $nextPosition = ($request->user()->todoLists()->max('position') ?? -1) + 1;

        $list = $request->user()->todoLists()->create([
            'name' => $validated['name'],
            'position' => $nextPosition,
        ]);

        return redirect()->route('lists.show', $list);
    }

    public function update(Request $request, TodoList $list): RedirectResponse
    {
        $this->authorizeList($request, $list);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $list->update($validated);

        return redirect()->route('lists.show', $list);
    }

    public function destroy(Request $request, TodoList $list): RedirectResponse
    {
        $this->authorizeList($request, $list);

        $list->delete();

        return redirect()->route('lists.index');
    }

    protected function authorizeList(Request $request, TodoList $list): void
    {
        abort_unless($list->user_id === $request->user()->id, 404);
    }
}
