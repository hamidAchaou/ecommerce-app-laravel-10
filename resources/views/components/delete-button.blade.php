@props(['route'])

@php
    $formId = 'delete-form-' . md5($route);
@endphp

<button
    type="button"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 text-red-600 hover:text-red-700 transition']) }}
    onclick="confirmDelete('{{ $route }}', '{{ $formId }}')"
>
    <i class="fas fa-trash"></i> Supprimer
</button>

<form method="POST" action="{{ $route }}" id="{{ $formId }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>
