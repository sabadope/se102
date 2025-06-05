<x-guest-layout>
    <style>
        /* Hide all scrollbars */
        ::-webkit-scrollbar {
            display: none;
        }
        
        /* For Firefox */
        * {
            scrollbar-width: none;
        }
        
        /* For IE and Edge */
        * {
            -ms-overflow-style: none;
        }
    </style>

    <!-- Back Button Container -->
    <div class="w-full" style="position: absolute; top: 25px; left: 25px;">
        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-600 hover:text-gray-800 transition duration-150 ease-in-out rounded-lg shadow-sm">
            {{ __('Home') }}
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-2 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me and Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div>
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
