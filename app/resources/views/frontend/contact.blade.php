@extends('layouts.app')

@section('title', 'Contact Us - ' . config('app.name'))

@section('content')

<section class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Get in touch with us
            </h2>
            <p class="mt-4 text-lg text-gray-600">
                Have a question, comment, or special request? We'd love to hear from you.
            </p>
        </div>
        
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- Contact Information Section --}}
            <div class="col-span-1 lg:col-span-1 bg-gray-50 rounded-lg p-6 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900">
                    Contact Information
                </h3>
                <p class="mt-2 text-gray-600">
                    Feel free to reach out to us through our various channels.
                </p>
                <div class="mt-6 space-y-4">
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.9 5.3c.7.5 1.6.5 2.3 0L21 8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8V6a2 2 0 00-2-2H5a2 2 0 00-2 2v2M21 8l-9 6-9-6"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-lg font-medium text-gray-900">Email</h4>
                            <p class="text-gray-600">
                                <a href="mailto:info@{{ config('app.name') }}.com" class="text-red-600 hover:text-red-700">info@{{ config('app.name') }}.com</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.95.69l.7 2.1a1 1 0 00.9.69h3.76a1 1 0 00.9-.69l.7-2.1A1 1 0 0115.72 3H19a2 2 0 012 2v14a2 2 0 01-2 2h-3.28a1 1 0 01-.95-.69l-.7-2.1a1 1 0 00-.9-.69h-3.76a1 1 0 00-.9.69l-.7 2.1A1 1 0 018.28 21H5a2 2 0 01-2-2V5z"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-lg font-medium text-gray-900">Phone</h4>
                            <p class="text-gray-600">+212 5 22 123456</p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Contact Form Section --}}
            <div class="md:col-span-2 bg-white rounded-lg p-8 shadow-md border border-gray-200">
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" id="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection