@extends('layouts.admin')

@section('title', 'User Management | INDI Lab Admin')

@section('admin_title', 'User Management')
@section('admin_subtitle', 'Manage administrative access and roles')

@section('admin_actions')
<a href="{{ route('admin.users.create') }}" class="btn-primary" style="background: var(--dashboard-primary); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
    <i class="fas fa-plus"></i> Add New User
</a>
@endsection

@section('admin_content')
<div class="contact-list-card">
    <table class="contact-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--dashboard-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $user->name }}</div>
                            <div style="font-size: 0.8rem; color: var(--dashboard-text-muted);">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span style="font-size: 0.75rem; padding: 4px 10px; background: rgba(255,255,255,0.05); border-radius: 20px; text-transform: capitalize;">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </td>
                <td>
                    <span class="status-badge {{ $user->is_active ? 'replied' : 'new' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <span style="font-size: 0.85rem; color: var(--dashboard-text-muted);">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-range" style="padding: 5px 12px; border: 1px solid var(--dashboard-border); color: #fff; text-decoration: none; border-radius: 4px; font-size: 0.8rem;"><i class="fas fa-edit"></i> Edit</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding: 5px 12px; border: 1px solid #dc3545; color: #dc3545; background: transparent; border-radius: 4px; font-size: 0.8rem; cursor: pointer;"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
