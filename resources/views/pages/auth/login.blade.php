@extends('layouts.app')

@section('title', 'Login')

@push('styles')
    <style>
        /* Hide sidebar and navbar on login page */
        .sidebar-container,
        .navbar {
            display: none;
        }

        .content {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .login-card {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-logo {
            display: block;
            margin: 0 auto 1rem auto;
            width: 100px;
            height: 100px;
            background: url('/images/1744210854.png') no-repeat center center;
            background-size: contain;
        }
    </style>
@endpush

@section('content')
    <div class="login-card">
        <div class="login-logo"></div>
        <h3 class="text-center mb-4">Login to Your Account</h3>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                    name="username" value="{{ old('username') }}" required autofocus>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
@endsection