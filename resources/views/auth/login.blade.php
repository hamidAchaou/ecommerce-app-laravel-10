@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 sm:p-8 overflow-hidden transform transition-all duration-300 hover:shadow-2xl">
    
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
        <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
            @csrf
    
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username"
                    class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>
    
            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input id="password" name="password" type="password" required autocomplete="current-password"
                    class="appearance-none rounded-md relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white transition"
                    placeholder="********" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>
    
            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-900 dark:border-gray-700" />
                <label for="remember_me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 select-none">
                    {{ __('Remember me') }}
                </label>
            </div>
    
            <!-- Actions -->
            <div class="flex items-center justify-between">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-600 font-semibold transition">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
    
                <x-primary-button class="ml-4 px-6 py-3 text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 rounded-md shadow-md transition font-semibold">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
</div>
@endsection
