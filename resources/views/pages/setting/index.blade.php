@extends('layouts.app')

@section('content')

    <div class="container mt-4">

        <h1 class="mb-4 text-center">Daftar Pengguna</h1>

        <a href="{{ route('setting.create') }}" class="btn btn-primary mb-3 shadow-sm">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </a>

        <div class="table-responsive">

            <table class="table table-hover table-bordered shadow-sm">

                <thead class="thead-light">

                    <tr>

                        <th>Nama</th>

                        <th>Email</th>

                        <th>Role</th>

                        <th>Status</th>

                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($users as $user)

                        <tr>

                            <td>{{ $user->name }}</td>

                            <td>{{ $user->email }}</td>

                            <td>{{ ucfirst($user->role) }}</td>

                            <td>
                                @if($user->active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('setting.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <form action="{{ route('setting.toggleActive', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->active)
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Nonaktifkan Pengguna">
                                            <i class="fas fa-user-slash"></i> Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success btn-sm" title="Aktifkan Pengguna">
                                            <i class="fas fa-user-check"></i> Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

            <div class="d-flex justify-content-center">
                {!! $users->links('pagination::bootstrap-4') !!}
            </div>

        </div>

    </div>

@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        </script>
    @endif
@endpush