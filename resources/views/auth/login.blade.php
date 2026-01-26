@extends('layouts.app')

@section('title', 'Login | INDI Lab')

@section('body-class', 'dark-theme')

@section('content')
<section class="title-section-video" style="padding-top: 150px;">
    <div class="Title">
        <h1>Admin Login</h1>
    </div>
</section>

<section class="TextLarge" style="position: relative; z-index: 10; padding-top: 50px; background-color: #1a1a1a; min-height: 80vh;">
    <div style="max-width: 400px; margin: 0 auto; background: #222; padding: 30px; border-radius: 8px; border: 1px solid #333;">
        @if ($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; color: white; margin-bottom: 5px;">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus style="width: 100%; padding: 10px; background: #333; border: 1px solid #555; color: white;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; color: white; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" id="password" class="form-control" required style="width: 100%; padding: 10px; background: #333; border: 1px solid #555; color: white;">
            </div>

            <button type="submit" style="width: 100%; background: white; color: black; padding: 10px; border: none; cursor: pointer; font-weight: bold; margin-top: 10px;">Log in</button>
        </form>
    </div>
</section>
@endsection
