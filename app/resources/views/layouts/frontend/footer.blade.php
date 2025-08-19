<!-- Footer -->
<footer class="bg-gray-900 text-gray-200 relative" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Footer</h2>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- Brand / About --}}
        <div>
            <h3 class="text-2xl font-bold text-red-600 mb-4">AtlasShoop</h3>
            <p class="text-gray-400 text-sm leading-relaxed">
                Traditional Industry in Morocco – Discover authentic Moroccan craftsmanship, from pottery and weaving to jewelry, woodwork, and more.  
                <span class="hidden md:inline">Support fair trade and local artisans while exploring unique products.</span>
            </p>
        </div>

        {{-- Navigation Links --}}
        <nav aria-label="Footer Navigation">
            <h4 class="text-xl font-semibold mb-4">Quick Links</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a></li>
                <li><a href="{{ route('categories.index') }}" class="hover:text-red-600 transition-colors">Categories</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-red-600 transition-colors">Products</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-red-600 transition-colors">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-red-600 transition-colors">Contact</a></li>
            </ul>
        </nav>

        {{-- Contact Info --}}
        <div>
            <h4 class="text-xl font-semibold mb-4">Contact</h4>
            <address class="not-italic text-sm space-y-1">
                <p>Email: <a href="mailto:info@atlas-shoop.com" class="hover:text-red-600">info@atlas-shoop.com</a></p>
                <p>Phone: <a href="tel:+212600000000" class="hover:text-red-600">+212 600 000 000</a></p>
                <p>Address: 123 Medina Street, Marrakech, Morocco</p>
            </address>
        </div>

        {{-- Social Media --}}
        <div>
            <h4 class="text-xl font-semibold mb-4">Follow Us</h4>
            <ul class="flex space-x-4 text-lg">
                <li>
                    <a href="#" target="_blank" rel="noopener" aria-label="Facebook" class="hover:text-red-600 transition-colors">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="#" target="_blank" rel="noopener" aria-label="Twitter" class="hover:text-red-600 transition-colors">
                        <i class="fab fa-twitter" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="#" target="_blank" rel="noopener" aria-label="Instagram" class="hover:text-red-600 transition-colors">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                </li>
                <li>
                    <a href="#" target="_blank" rel="noopener" aria-label="Pinterest" class="hover:text-red-600 transition-colors">
                        <i class="fab fa-pinterest-p" aria-hidden="true"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-gray-800 mt-8 py-4 text-center text-gray-500 text-sm flex flex-col md:flex-row md:justify-between md:items-center px-4 sm:px-6 lg:px-8">
        <p>&copy; {{ date('Y') }} AtlasShoop. All rights reserved.</p>
        <p class="mt-2 md:mt-0">Designed with ❤️ in Morocco</p>
    </div>

    {{-- Decorative Pattern --}}
    <div class="absolute inset-0 opacity-5 bg-[url('/assets/images/pattern-morocco.webp')] bg-repeat pointer-events-none"></div>
</footer>
