@extends('layouts.app')

@section('title', 'Contact Us - My Website')
@section('meta_description', 'Get in touch with us for inquiries, support, or collaborations. We are here to help you!')

@section('content')
<div class="bg-white dark:bg-gray-900 min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-morocco-blue text-white py-20">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold mb-4">Contact Us</h1>
            <p class="text-lg sm:text-xl text-morocco-ivory/90">
                We’d love to hear from you! Reach out for support, questions, or partnerships.
            </p>
        </div>
    </section>

    <!-- Contact Form & Info -->
    <section class="py-16">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <!-- Contact Form -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Send us a Message</h2>
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Name</label>
                        <input type="text" name="name" id="name" required
                            class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 shadow-sm focus:border-morocco-red focus:ring-morocco-red sm:text-sm dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email" required
                            class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 shadow-sm focus:border-morocco-green focus:ring-morocco-green sm:text-sm dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea name="message" id="message" rows="5" required
                            class="mt-2 block w-full rounded-lg border-gray-300 dark:border-gray-700 shadow-sm focus:border-morocco-blue focus:ring-morocco-blue sm:text-sm dark:bg-gray-900 dark:text-gray-100"></textarea>
                    </div>
                    
                    <button type="submit"
                        class="w-full bg-morocco-red hover:bg-red-700 text-white py-3 px-6 rounded-lg font-semibold shadow-md transition duration-300">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="flex flex-col justify-center space-y-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Our Office</h3>
                    <p class="text-gray-600 dark:text-gray-400">123 Avenue Mohammed V, Rabat, Morocco</p>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Phone</h3>
                    <p class="text-gray-600 dark:text-gray-400">+212 6 12 34 56 78</p>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Email</h3>
                    <p class="text-gray-600 dark:text-gray-400">contact@mywebsite.com</p>
                </div>
                
                <!-- Social Links -->
                <div class="flex space-x-5 mt-4">
                    <a href="https://facebook.com" target="_blank" class="text-morocco-blue hover:text-blue-600">
                        <i class="fab fa-facebook text-2xl"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="text-morocco-blue hover:text-blue-400">
                        <i class="fab fa-twitter text-2xl"></i>
                    </a>
                    <a href="https://linkedin.com" target="_blank" class="text-morocco-blue hover:text-blue-700">
                        <i class="fab fa-linkedin text-2xl"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" class="text-morocco-blue hover:text-pink-600">
                        <i class="fab fa-instagram text-2xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Google Map -->
    <section class="relative h-96">
        <iframe 
            class="w-full h-full rounded-t-2xl"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3332.521078711674!2d-6.8416509!3d33.9715906!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xda76c82cd34f!2sRabat%2C%20Morocco!5e0!3m2!1sen!2sma!4v1699999999999!5m2!1sen!2sma" 
            style="border:0;" allowfullscreen="" loading="lazy">
        </iframe>
    </section>
</div>
@endsection
