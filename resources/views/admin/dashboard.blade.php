@extends('layouts.app')

@section('title', 'Admin Dashboard | INDI Lab')

@section('body-class', 'dark-theme')

@section('content')
<section class="title-section-video" style="padding-top: 150px;">
    <div class="Title">
        <h1>Dashboard</h1>
    </div>
</section>

<section class="TextLarge" style="position: relative; z-index: 10; padding-top: 50px; background-color: #1a1a1a; min-height: 80vh;">
    <div style="max-width: 800px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="color: white; margin: 0;">Welcome, {{ Auth::user()->name ?? 'Admin' }}</h2>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background: transparent; border: 1px solid #555; color: white; padding: 5px 15px; cursor: pointer;">Logout</button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <!-- Projects Card -->
            <div style="background: #222; padding: 20px; border-radius: 8px; border: 1px solid #333;">
                <h3 style="color: white; margin-top: 0;">Projects</h3>
                <p style="color: #aaa;">Manage your portfolio work.</p>
                <div style="margin-top: 15px;">
                    <a href="{{ route('work.index') }}" style="display: block; color: #007bff; margin-bottom: 10px;">View All Projects &rarr;</a>
                    <a href="{{ route('work.create') }}" style="display: block; color: #28a745;">+ Add New Project</a>
                </div>
            </div>

            <!-- Other Admin Cards can go here -->
        </div>

    </div>
</section>
@endsection
