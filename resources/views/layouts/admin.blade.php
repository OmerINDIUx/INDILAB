@extends('layouts.app')

@section('body-class', 'dashboard-page')

@push('css')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    @include('admin.partials.sidebar')
    
    <main class="dashboard-main">
        @include('admin.partials.header')
        
        <div class="dashboard-content">
            @if(session('success'))
                <div style="background: rgba(40,167,69,0.1); border: 1px solid #28a745; color: #28a745; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: rgba(220,53,69,0.1); border: 1px solid #dc3545; color: #dc3545; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('admin_content')
        </div>
    </main>
</div>
@endsection
