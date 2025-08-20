@extends('layouts.app')

@section('title', 'Register | ATlasShoop – Traditional Moroccan Industry')
@section('description', 'Create your account on ATlasShoop and enjoy authentic Moroccan traditional crafts shopping experience: pottery, carpets, leather, silver, and more.')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-morocco-ivory py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <!-- Right side (Image + Branding + Welcome text) -->
        <div class="hidden md:flex flex-col justify-center items-center bg-morocco-red text-white p-10">
            <img src="{{ asset('assets/images/logo-removebg.webp') }}" alt="ATlasShoop Logo" class="w-24 h-24 mb-6">
            <h2 class="text-2xl font-bold">Welcome to ATlasShoop</h2>
            <p class="mt-4 text-sm text-morocco-ivory text-center">
                Discover the beauty of Moroccan traditional crafts and join us for an authentic shopping experience.
            </p>
        </div>

        <!-- Left side (Register Form) -->
        <div class="flex flex-col justify-center p-8 sm:p-12">
            <h1 class="text-2xl font-bold text-morocco-blue mb-6">Create an Account</h1>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Full Name')" class="text-morocco-green"/>
                    <x-text-input id="name" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-morocco-blue focus:ring focus:ring-morocco-blue/30" 
                                  type="text" name="name" :value="old('name')" required autofocus autocomplete="name"/>
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500"/>
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email Address')" class="text-morocco-green"/>
                    <x-text-input id="email" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-morocco-blue focus:ring focus:ring-morocco-blue/30" 
                                  type="email" name="email" :value="old('email')" required autocomplete="username"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500"/>
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-morocco-green"/>
                    <x-text-input id="password" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-morocco-blue focus:ring focus:ring-morocco-blue/30"
                                  type="password" name="password" required autocomplete="new-password"/>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500"/>
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-morocco-green"/>
                    <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-xl border-gray-300 focus:border-morocco-blue focus:ring focus:ring-morocco-blue/30"
                                  type="password" name="password_confirmation" required autocomplete="new-password"/>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500"/>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-sm text-morocco-blue hover:underline">
                        Already have an account?
                    </a>
                    <x-primary-button class="bg-morocco-green hover:bg-morocco-blue px-6 py-2 rounded-xl shadow-md transition">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
