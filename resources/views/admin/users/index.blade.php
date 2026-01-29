@extends('layouts.app')

@section('title', 'User Management | INDI Lab Admin')

@push('css')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('body-class', 'dashboard-page')

@section('content')
<div class="dashboard-container">
    <aside class="dashboard-sidebar">
        <!-- Re-using sidebar from previous views -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <span style="font-weight: 800; letter-spacing: -1px; font-size: 1.5rem;">INDI Lab</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fas fa-th-large"></i><span>Overview</span></a>
                <a href="{{ route('admin.analytics.index') }}" class="nav-item"><i class="fas fa-chart-line"></i><span>Analytics</span></a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">Content</div>
                <a href="{{ route('work.index') }}" class="nav-item"><i class="fas fa-briefcase"></i><span>Projects</span></a>
                <a href="{{ route('admin.contacts.index') }}" class="nav-item"><i class="fas fa-envelope"></i><span>Contacts</span></a>
                <a href="{{ route('admin.media.index') }}" class="nav-item"><i class="fas fa-images"></i><span>Media</span></a>
            </div>
            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="{{ route('admin.users.index') }}" class="nav-item active"><i class="fas fa-users"></i><span>Users</span></a>
            </div>
        </nav>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-title">
                <h1>User Management</h1>
                <p class="header-subtitle">Manage administrative access and roles</p>
            </div>
        </header>

        <div class="dashboard-content">
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
                                    <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--dashboard-primary); display: flex; align-items: center; justify-content: center; font-weight: 700;">
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
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-range" style="padding: 5px 12px; border: 1px solid var(--dashboard-border); color: #fff; text-decoration: none; border-radius: 4px; font-size: 0.8rem;"><i class="fas fa-edit"></i> Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
