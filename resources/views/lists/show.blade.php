<x-app-layout :lists="$lists" :active-list="$list">
    <div class="max-w-2xl mx-auto px-6 sm:px-10 py-10">
        <div class="flex items-center justify-between gap-4 mb-6" x-data="{ renaming: false }">
            <form method="POST" action="{{ route('lists.update', $list) }}" class="flex-1 min-w-0" x-show="renaming" x-cloak>
                @csrf
                @method('PATCH')
                <input type="text" name="name" value="{{ $list->name }}" maxlength="255" required
                    class="text-2xl font-semibold w-full bg-transparent border-b"
                    style="color: var(--ink); border-color: var(--line);"
                    @blur="$el.form.requestSubmit()" x-init="$nextTick(() => $el.focus())">
            </form>

            <h1 class="text-2xl font-semibold truncate" style="color: var(--ink);" x-show="!renaming" @click="renaming = true">
                {{ $list->name }}
            </h1>

            <form method="POST" action="{{ route('lists.destroy', $list) }}"
                onsubmit="return confirm('{{ __('Delete this list and everything in it?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs" style="color: var(--ink-soft);"
                    onmouseover="this.style.color='var(--error-red)'" onmouseout="this.style.color='var(--ink-soft)'">
                    {{ __('Delete list') }}
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('tasks.store', $list) }}" class="mb-6 flex items-center gap-2">
            @csrf
            <input type="text" name="title" required maxlength="255" placeholder="{{ __('Add a task…') }}"
                class="td-input-plain text-base" style="border-bottom: 1px solid var(--line);">
        </form>
        @error('title')
            <p class="td-error -mt-4 mb-4">{{ $message }}</p>
        @enderror

        <div>
            @forelse ($openTasks as $task)
                <div class="td-task-row">
                    <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="td-checkbox" aria-label="{{ __('Mark done') }}"></button>
                    </form>

                    <form method="POST" action="{{ route('tasks.update', $task) }}" class="flex-1 min-w-0">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="title" value="{{ $task->title }}" maxlength="255"
                            class="td-input-plain td-task-title" style="padding: 0;"
                            onchange="this.form.requestSubmit()">
                    </form>

                    <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs" style="color: var(--ink-soft);"
                            onmouseover="this.style.color='var(--error-red)'" onmouseout="this.style.color='var(--ink-soft)'">
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm py-6" style="color: var(--ink-soft);">
                    {{ __('Nothing here yet — add your first task above.') }}
                </p>
            @endforelse
        </div>

        @if ($completedTasks->isNotEmpty())
            <div class="mt-8" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="text-xs font-semibold uppercase tracking-wide"
                    style="color: var(--ink-soft);">
                    {{ __('Completed') }} ({{ $completedTasks->count() }})
                </button>

                <div x-show="open" x-cloak class="mt-2">
                    @foreach ($completedTasks as $task)
                        <div class="td-task-row">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="td-checkbox" checked aria-label="{{ __('Mark not done') }}"></button>
                            </form>

                            <p class="flex-1 min-w-0 td-task-title is-done" style="padding: 4px 2px;">
                                {{ $task->title }}
                            </p>

                            <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs" style="color: var(--ink-soft);">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
