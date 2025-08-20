@extends('layouts.app')

@section('title', 'Login | ATlasShoop – Traditional Moroccan Industry')
@section('description', 'Log in to your ATlasShoop account and explore authentic Moroccan traditional crafts: carpets, pottery, leather, jewelry, and more.')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-morocco-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-xl rounded-xl p-8 transform transition-all duration-300 hover:shadow-2xl">
        
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email Address')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input 
                    id="email" 
                    name="email" 
                    type="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="appearance-none rounded-lg w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 
                           focus:outline-none focus:ring-2 focus:ring-morocco-blue focus:border-morocco-blue dark:bg-gray-900 dark:text-white transition" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
                <x-text-input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="********"
                    class="appearance-none rounded-lg w-full px-4 py-3 border border-gray-300 dark:border-gray-700 placeholder-gray-400 dark:placeholder-gray-500 
                           focus:outline-none focus:ring-2 focus:ring-morocco-blue focus:border-morocco-blue dark:bg-gray-900 dark:text-white transition" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600 dark:text-red-400" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox"
                    class="h-4 w-4 text-morocco-green focus:ring-morocco-blue border-gray-300 rounded dark:bg-gray-900 dark:border-gray-700" />
                <label for="remember_me" class="ml-2 text-sm text-gray-700 dark:text-gray-300 select-none">
                    {{ __('Remember me') }}
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-semibold text-morocco-blue hover:underline transition">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button 
                    class="ml-4 px-6 py-3 bg-morocco-green hover:bg-morocco-blue text-white font-semibold rounded-lg shadow-md transition">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
