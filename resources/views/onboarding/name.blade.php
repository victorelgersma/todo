<x-guest-layout>
    <p class="text-sm mb-4" style="color: var(--ink-soft);">
        {{ __('What should we call you?') }}
    </p>

    <form method="POST" action="{{ route('onboarding.name.update') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="text-xs font-semibold uppercase tracking-wide" style="color: var(--ink-soft);">{{ __('Name') }}</label>
            <input id="name" class="td-input mt-1" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <p class="td-error mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end pt-2">
            <button type="submit" class="td-btn td-btn-solid">{{ __('Continue') }}</button>
        </div>
    </form>
</x-guest-layout>
