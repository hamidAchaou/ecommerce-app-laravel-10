{{-- Contact Form --}}
<div class="bg-white p-8 rounded-xl shadow-md">
    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
        </div>

        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" id="message" rows="4" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
        </div>

        <button type="submit" class="w-full btn btn-primary">
            Send Message
        </button>
    </form>
</div>
