<form action="{{ route('roles.store') }}" method="POST">
    @csrf
    <input type="text" name="name" placeholder="Role name" />
    
    @foreach($permissions as $permission)
        <label>
            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
            {{ $permission->name }}
        </label>
    @endforeach

    <button type="submit">Create Role</button>
</form>
