@extends('layouts.admin')

@section('title', 'Edit User | INDI Lab Admin')

@section('admin_title', 'Edit User')
@section('admin_subtitle', 'Modify account details for ' . $user->name)

@section('admin_content')
<div style="max-width: 600px; margin: 0 auto;">
    <a href="{{ route('admin.users.index') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--dashboard-text-muted); text-decoration: none; margin-bottom: 20px; font-size: 0.9rem;">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>

    <div style="background: var(--dashboard-card-bg); border: 1px solid var(--dashboard-border); border-radius: 12px; padding: 30px;">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                    style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                @error('name') <span style="color: #dc3545; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                    style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                @error('email') <span style="color: #dc3545; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Role</label>
                    <select name="role" required 
                        style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none; cursor: pointer;">
                        <option value="editor" {{ old('role', $user->role) == 'editor' ? 'selected' : '' }}>Editor (Blog Only)</option>
                        <option value="analytics" {{ old('role', $user->role) == 'analytics' ? 'selected' : '' }}>Analytics Viewer</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Status</label>
                    <select name="is_active" required 
                        style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none; cursor: pointer;">
                        <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="border-top: 1px solid var(--dashboard-border); margin: 30px 0; padding-top: 20px;">
                <h4 style="margin-bottom: 15px; font-size: 1rem;">Change Password</h4>
                <p style="font-size: 0.8rem; color: var(--dashboard-text-muted); margin-bottom: 20px;">Leave blank if you don't want to change the password.</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">New Password</label>
                        <input type="password" name="password" 
                            style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                        @error('password') <span style="color: #dc3545; font-size: 0.8rem; margin-top: 5px; display: block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">Confirm New Password</label>
                        <input type="password" name="password_confirmation" 
                            style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--dashboard-border); border-radius: 8px; color: #fff; outline: none;">
                    </div>
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: var(--dashboard-primary); color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 1rem;">
                Update Account Details
            </button>
        </form>
    </div>
</div>
@endsection
