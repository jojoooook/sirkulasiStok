@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">Edit Pengguna</h1>

        <form action="{{ route('setting.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}"
                    required readonly>
            </div>

            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" class="form-control" required>
                    <option value="karyawan" {{ $user->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <!-- Form untuk Ubah Password -->
            <hr>
            <h4>Ubah Password</h4>

            <div class="form-group">
                <label for="current_password">Password Lama</label>
                <input type="password" name="current_password"
                    class="form-control @error('current_password') is-invalid @enderror"
                    placeholder="Masukkan password lama">
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan password baru">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control"
                    placeholder="Konfirmasi password baru">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Pengguna</button>
        </form>

        <!-- Tombol Reset Password -->
        <form action="{{ route('setting.resetPassword', $user->id) }}" method="POST" class="mt-3">
            @csrf
            <button type="button" id="reset-password-button" class="btn btn-danger">Reset Password</button>
        </form>
    </div>
@endsection

@push('scripts')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <script>
        document.getElementById('reset-password-button').addEventListener('click', function () {
            // Konfirmasi reset password
            Swal.fire({
                title: 'Yakin ingin mereset password?',
                text: "Password akan diubah menjadi 123456!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, reset password',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna mengonfirmasi, kirim form untuk reset password
                    this.closest('form').submit();
                }
            });
        });
    </script>
@endpush