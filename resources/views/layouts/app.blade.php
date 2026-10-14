
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

<body x-data="{ sidebarOpen: false }" x-init="$watch('sidebarOpen', open => { document.documentElement.style.overflow = open ? 'hidden' : '' })">
    <div class="flex min-h-screen">
        {{-- Mobile top bar: only shown below sm, gives access to the drawer --}}
        <div class="sm:hidden fixed top-0 inset-x-0 z-30 flex items-center justify-between px-4 py-3"
            style="background: var(--sidebar); border-bottom: 1px solid var(--line);">
            <button type="button" @click="sidebarOpen = true" aria-label="{{ __('Open menu') }}" class="p-1 -ml-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" style="color: var(--ink);">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="font-semibold text-sm truncate px-2" style="color: var(--ink);">
                {{ $activeList->name ?? (request()->routeIs('lists.overview') ? __('Overview') : config('app.name', 'To-Do')) }}
            </span>
            <span class="w-8"></span>
        </div>

        {{-- Backdrop, mobile only --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/40 sm:hidden" style="display:none;" x-cloak></div>

        {{-- Sidebar / drawer --}}
        <aside
            class="td-sidebar w-64 shrink-0 flex flex-col p-4 fixed inset-y-0 left-0 z-50 transform -translate-x-full transition-transform duration-200 ease-in-out sm:static sm:flex sm:translate-x-0 sm:z-auto"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center justify-between mb-6 px-1">
                <span class="font-semibold text-sm" style="color: var(--ink);">{{ config('app.name', 'To-Do') }}</span>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs" style="color: var(--ink-soft);">
                            {{ __('Log out') }}
                        </button>
                    </form>

                    <button type="button" @click="sidebarOpen = false" aria-label="{{ __('Close menu') }}" class="sm:hidden p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" style="color: var(--ink-soft);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto">
                <a href="{{ route('lists.overview') }}"
                    @click="sidebarOpen = false"
                    class="td-sidebar-link mb-3 {{ request()->routeIs('lists.overview') ? 'is-active' : '' }}">
                    <span>{{ __('Overview') }}</span>
                </a>

                @foreach ($lists as $sidebarList)
                    <a href="{{ route('lists.show', $sidebarList) }}"
                        @click="sidebarOpen = false"
                        class="td-sidebar-link {{ ($activeList && $activeList->id === $sidebarList->id) ? 'is-active' : '' }}">
                        <span class="truncate">{{ $sidebarList->name }}</span>
                        <span class="text-xs" style="color: var(--ink-soft);">
                            {{ $sidebarList->open_tasks_count ?: '' }}
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

        <main class="flex-1 min-w-0 pt-14 sm:pt-0">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
