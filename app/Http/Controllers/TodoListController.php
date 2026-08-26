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

    public function show(Request $request, TodoList $list): View
    {
        $this->authorizeList($request, $list);

        return view('lists.show', [
            'lists' => $request->user()->todoLists()->orderBy('position')->get(),
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
