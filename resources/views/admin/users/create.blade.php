@extends('layouts.admin')

@section('title', 'Add New User | INDI Lab Admin')

@section('admin_title', 'Add New User')
@section('admin_subtitle', 'Create a new administrative account')

@section('admin_content')
<div style="max-width: 600px; margin: 0 auto;">
    <a href="{{ route('admin.users.index') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--dashboard-text-muted); text-decoration: none; margin-bottom: 20px; font-size: 0.9rem;">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>

    <div style="background: var(--dashboard-card-bg); border: 1px solid var(--dashboard-border); border-radius: 12px; padding: 30px;">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                    style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                @error('name') <span style="color: #dc3545; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                @error('email') <span style="color: #dc3545; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Role</label>
                    <select name="role" required 
                        style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none; cursor: pointer;">
                        <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor (Blog Only)</option>
                        <option value="analytics" {{ old('role') == 'analytics' ? 'selected' : '' }}>Analytics Viewer</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Status</label>
                    <select name="is_active" required 
                        style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none; cursor: pointer;">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Password</label>
                    <input type="password" name="password" required 
                        style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                    @error('password') <span style="color: #dc3545; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Confirm Password</label>
                    <input type="password" name="password_confirmation" required 
                        style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: var(--dashboard-primary); color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 1rem;">
                Create Member Account
            </button>
        </form>
    </div>
</div>
@endsection
