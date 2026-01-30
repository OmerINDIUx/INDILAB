<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">INDI Lab</a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Overview</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="nav-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Content</div>
            <a href="{{ route('work.index') }}" class="nav-item {{ request()->routeIs('work.*') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i>
                <span>Projects</span>
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="nav-item {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i>
                <span>Contacts</span>
                @if(isset($newContactsCount) && $newContactsCount > 0)
                <span class="nav-badge">{{ $newContactsCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.media.index') }}" class="nav-item {{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                <i class="fas fa-images"></i>
                <span>Media</span>
            </a>
        </div>
        
        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
        <div class="nav-section">
            <div class="nav-section-title">Management</div>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        @endif
    </nav>
</aside>
