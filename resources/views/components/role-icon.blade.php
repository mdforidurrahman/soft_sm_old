<div>
    @php
        $role = $userRole->roles->first()->name ?? 'guest';
        $icons = [
            'admin' => 'fas fa-user-shield',
            'editor' => 'fas fa-edit',
            'subscriber' => 'fas fa-user',
            'guest' => 'fas fa-user-secret',
        ];
    @endphp

    <i class="{{ $icons[$role] ?? 'fas fa-user' }}"></i>
    {{ ucfirst($role) }}
</div>
