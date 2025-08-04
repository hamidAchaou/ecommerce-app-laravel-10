@props(['message'])

@if($errors->any() || $message)
    <div {{ $attributes->merge(['class' => 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center']) }} role="alert">
        <svg class="fill-current w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M14.7 5.3a1 1 0 00-1.4 0L10 8.59 6.7 5.3a1 1 0 00-1.4 1.4L8.59 10l-3.3 3.3a1 1 0 101.4 1.4L10 11.41l3.3 3.3a1 1 0 001.4-1.4L11.41 10l3.3-3.3a1 1 0 000-1.4z"/></svg>
        <div>
            @if($errors->any())
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @else
                <span>{{ $message }}</span>
            @endif
        </div>
    </div>
@endif
