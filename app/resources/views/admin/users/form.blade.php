@php
    // For create, $user may not be set
    $user = $user ?? null;
@endphp

{{-- Name --}}
<div>
    <label for="name" class="block font-medium text-sm text-gray-700">Nom</label>
    <input id="name" name="name" type="text"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
        value="{{ old('name', $user->name ?? '') }}" required autofocus>
</div>

{{-- Email --}}
<div>
    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
    <input id="email" name="email" type="email"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
        value="{{ old('email', $user->email ?? '') }}" required>
</div>

{{-- Password --}}
<div>
    <label for="password" class="block font-medium text-sm text-gray-700">
        Mot de passe
        @if($user)
            <small class="text-gray-500">(laisser vide pour ne pas changer)</small>
        @endif
    </label>
    <input id="password" name="password" type="password"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
        {{ $user ? '' : 'required' }}
        autocomplete="new-password">
</div>

{{-- Password Confirmation --}}
<div>
    <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmer le mot de passe</label>
    <input id="password_confirmation" name="password_confirmation" type="password"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"
        {{ $user ? '' : 'required' }}
        autocomplete="new-password">
</div>

{{-- Roles --}}
<div class="mb-6">
    <x-input-label for="roles" value="Rôles" />

    <select 
        name="roles[]" 
        id="roles" 
        multiple 
        class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    >
        @foreach ($roles as $role)
            <option value="{{ $role->name }}"
                @if(in_array($role->name, old('roles', $user ? $user->roles->pluck('name')->toArray() : []))) selected @endif>
                {{ $role->name }}
            </option>
        @endforeach
    </select>

    @error('roles')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Permissions --}}
<div class="mb-6">
    <x-input-label for="permissions" value="Permissions" />

    <select 
        name="permissions[]" 
        id="permissions" 
        multiple 
        class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    >
        @foreach ($permissions as $permission)
            <option value="{{ $permission->name }}"
                @if(in_array($permission->name, old('permissions', $user ? $user->permissions->pluck('name')->toArray() : []))) selected @endif>
                {{ $permission->name }}
            </option>
        @endforeach
    </select>

    @error('permissions')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>

