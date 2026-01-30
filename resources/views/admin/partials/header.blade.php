<header class="dashboard-header">
    <div class="header-title">
        <h1>@yield('admin_title', 'Dashboard')</h1>
        <p class="header-subtitle">@yield('admin_subtitle', 'Welcome back, ' . auth()->user()->name)</p>
    </div>
    
    <div class="header-actions">
        <!-- Page specific actions -->
        @yield('admin_actions')

        <div class="user-menu">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'editor')) }}</div>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </div>
</header>
