@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Profile Header & Banner --}}
        <div class="relative bg-gradient-to-r from-primary-400 to-accent-500 h-48">
            {{-- This is a placeholder for a banner image --}}
        </div>

        <div class="p-8 -mt-24 flex flex-col lg:flex-row items-center lg:items-start lg:space-x-8">

            {{-- Left Column: Profile Picture & Actions --}}
            <div class="w-full lg:w-1/3 flex flex-col items-center">
                <div class="flex flex-col items-center text-center">
                    <div class="relative w-40 h-40 bg-gray-200 rounded-full border-4 border-white shadow-lg overflow-hidden">
                        <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" alt="{{ $user->name }}" class="object-cover w-full h-full">
                    </div>

                    <h1 class="mt-4 text-3xl font-extrabold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-sm text-gray-500 font-medium">Joined On {{ $user->created_at->format('M d, Y') }}</p>
                </div>

                <div class="mt-6 w-full flex flex-col space-y-3 items-center">
                    <x-button.primary-button href="{{ route('profile.edit') }}" icon="fas fa-pen" color="primary" class="w-full">
                        Edit Profile
                    </x-button.primary-button>
                    <x-button.primary-button href="{{ route('password.change') }}" icon="fas fa-key" color="secondary" class="w-full">
                        Change Password
                    </x-button.primary-button>
                </div>
            </div>

            {{-- Right Column: Main Content --}}
            <div class="w-full lg:w-2/3 mt-8 lg:mt-0">

                {{-- User Info Section --}}
                <div class="bg-gray-50 rounded-lg p-6 shadow-sm mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Account & Client Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-500">Email Address</p>
                            <p class="text-gray-900 font-semibold">{{ $user->email }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-500">Phone Number</p>
                            <p class="text-gray-900 font-semibold">{{ $user->client?->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-500">Country</p>
                            <p class="text-gray-900 font-semibold">{{ $user->client?->country?->name ?? 'Not provided' }}</p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-500">City</p>
                            <p class="text-gray-900 font-semibold">{{ $user->client?->city?->name ?? 'Not provided' }}</p>
                        </div>
                        <div class="space-y-2 col-span-1 md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Full Address</p>
                            <p class="text-gray-900 font-semibold">{{ $user->client?->full_address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Stats Section --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg p-6 text-center border border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Total Orders</p>
                        <p class="mt-1 text-4xl font-extrabold text-primary-600">{{ $user->client?->total_orders ?? 0 }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-6 text-center border border-gray-200">
                        <p class="text-sm font-medium text-gray-500">Total Spent</p>
                        <p class="mt-1 text-4xl font-extrabold text-primary-600">{{ number_format($user->client?->total_spent ?? 0, 2) }} MAD</p>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-button.primary-button href="{{ route('frontend.orders.index') }}" icon="fas fa-box" color="primary" class="w-full">
                        My Orders
                    </x-button.primary-button>
                    <x-button.primary-button href="{{ route('wishlist.index') }}" icon="fas fa-heart" color="secondary" class="w-full">
                        Wishlist
                    </x-button.primary-button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection