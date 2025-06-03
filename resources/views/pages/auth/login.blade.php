@extends('layouts.app')

@section('title', 'Login - CV Sri Lestari')

@push('styles')
    <style>
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
            background: linear-gradient(135deg, #6e7f85, #3b4d56);
            /* Mewah gradient background */
        }

        .login-card {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            margin: 0 20px;
        }

        .login-logo {
            display: block;
            margin: 0 auto 2rem auto;
            width: 120px;
            height: 120px;
            /* background: url('/images/1744210854.png') no-repeat center center; */
            background-size: contain;
        }

        h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 700;
            color: #333;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }

        .form-control {
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            color: #dc3545;
        }

        /* Responsiveness */
        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
            }

            .login-logo {
                width: 100px;
                height: 100px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="login-card">
        <div class="login-logo" style="background-image: url('/images/logo.png');"></div>
        <h3 class="text-center mb-4">Login to CV Sri Lestari</h3>

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

@push('scripts')
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: '{{ session('error') }}',
                confirmButtonText: 'Coba Lagi'
            });
        </script>
    @endif
@endpush