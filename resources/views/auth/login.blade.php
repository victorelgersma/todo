<x-guest-layout>
    @if (session('status') === 'login-link-sent')
        <div class="td-error" style="color: var(--ink-soft); background: var(--paper-card); padding: 10px 12px; border-radius: 6px; margin-bottom: 16px;">
            {{ __("Check your email — we've sent you a link to continue.") }}
        </div>
    @endif

    <p class="text-sm mb-4" style="color: var(--ink-soft);">
        {{ __("Enter your email. We'll send a link to log in — if you're new, an account is created automatically.") }}
    </p>

    <form method="POST" action="{{ route('login.link') }}" class="space-y-4"
        x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div style="position:absolute;left:-9999px;" aria-hidden="true">
            <label for="website">Leave this field empty</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>

        <div>
            <label for="email" class="text-xs font-semibold uppercase tracking-wide" style="color: var(--ink-soft);">{{ __('Email') }}</label>
            <input id="email" class="td-input mt-1" type="email" name="email" value="{{ old('email') }}" required
                autofocus autocomplete="username">
            @error('email')
                <p class="td-error mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end pt-2">
            <button type="submit" class="td-btn td-btn-solid" x-bind:disabled="submitting">
                <span x-show="!submitting">{{ __('Continue with email') }}</span>
                <span x-show="submitting" x-cloak>{{ __('Sending…') }}</span>
            </button>
        </div>
    </form>
</x-guest-layout>
