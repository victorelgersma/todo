<x-app-layout>
    <div class="max-w-3xl mx-auto px-6 sm:px-10 py-10">
        <h1 class="text-2xl font-semibold mb-1" style="color: var(--ink);">{{ __('Overview') }}</h1>
        <p class="text-sm mb-8" style="color: var(--ink-soft);">{{ __('Your lists, most recently active first.') }}</p>

        @if ($projects->isEmpty())
            <p class="text-sm py-6" style="color: var(--ink-soft);">
                {{ __("You don't have any lists yet. Use “+ New list” in the sidebar to create your first one.") }}
            </p>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($projects as $project)
                    <a href="{{ route('lists.show', $project) }}" class="td-project-card">
                        <div class="flex items-baseline justify-between gap-3">
                            <h2 class="font-semibold truncate" style="color: var(--ink);">{{ $project->name }}</h2>
                            <span class="text-xs shrink-0" style="color: var(--ink-soft);">
                                {{ $project->open_tasks_count }} {{ __('open') }}
                                @if ($project->completed_tasks_count)
                                    · {{ $project->completed_tasks_count }} {{ __('done') }}
                                @endif
                            </span>
                        </div>

                        <p class="text-xs mt-1" style="color: var(--ink-soft);">
                            @if ($project->last_task_added_at)
                                {{ __('Last task added :time', ['time' => $project->last_task_added_at->diffForHumans()]) }}
                            @else
                                {{ __('No tasks yet') }}
                            @endif
                        </p>

                        @if ($project->openTasks->isNotEmpty())
                            <ul class="mt-3 space-y-1">
                                @foreach ($project->openTasks as $task)
                                    <li class="flex items-center gap-2 text-sm truncate" style="color: var(--ink);">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: var(--ink-soft);"></span>
                                        <span class="truncate">{{ $task->title }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($project->open_tasks_count > $project->openTasks->count())
                                <p class="text-xs mt-2" style="color: var(--ink-soft);">
                                    {{ __('+ :count more', ['count' => $project->open_tasks_count - $project->openTasks->count()]) }}
                                </p>
                            @endif
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
