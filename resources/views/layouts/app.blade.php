<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'To-Do') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="flex min-h-screen">
        <aside class="td-sidebar w-64 shrink-0 flex flex-col p-4 hidden sm:flex">
            <div class="flex items-center justify-between mb-6 px-1">
                <span class="font-semibold text-sm" style="color: var(--ink);">{{ config('app.name', 'To-Do') }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs" style="color: var(--ink-soft);">
                        {{ __('Log out') }}
                    </button>
                </form>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto">
                @foreach ($lists as $sidebarList)
                    <a href="{{ route('lists.show', $sidebarList) }}"
                        class="td-sidebar-link {{ ($activeList && $activeList->id === $sidebarList->id) ? 'is-active' : '' }}">
                        <span class="truncate">{{ $sidebarList->name }}</span>
                        <span class="text-xs" style="color: var(--ink-soft);">
                            {{ $sidebarList->openTasks()->count() ?: '' }}
                        </span>
                    </a>
                @endforeach
            </nav>

            <form method="POST" action="{{ route('lists.store') }}" class="mt-4 pt-4 border-t"
                style="border-color: var(--line);" x-data="{ open: false, name: '' }">
                @csrf
                <template x-if="!open">
                    <button type="button" @click="open = true; $nextTick(() => $refs.name.focus())"
                        class="td-sidebar-link w-full text-left">
                        + {{ __('New list') }}
                    </button>
                </template>
                <template x-if="open">
                    <input x-ref="name" type="text" name="name" x-model="name" required maxlength="255"
                        placeholder="{{ __('List name') }}" class="td-input"
                        @keydown.escape="open = false" @blur="if (!name) open = false">
                </template>
            </form>
        </aside>

        <main class="flex-1 min-w-0">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
