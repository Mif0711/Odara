@extends('layouts.app')

@section('title', 'Akun Internal')
@section('page-title', 'Akun Internal')

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">Administrasi</span>
            <h1>Akun Internal</h1>
            <p>Kelola akun Admin dan Petugas yang dapat mengakses ODARA.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <svg class="icon"><use href="#i-plus"></use></svg>
                Tambah Akun
            </a>
        </div>
    </div>

    <div class="info-banner">
        <svg class="icon"><use href="#i-shield"></use></svg>
        <p>ODARA hanya memiliki akun internal Admin dan Petugas. Tidak ada akun atau registrasi pelanggan.</p>
    </div>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Daftar Akun</h2>
                <p>{{ $users->count() }} akun internal · {{ $users->where('is_active', true)->count() }} aktif</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="th-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td data-label="Nama">
                                <span class="officer-cell">
                                    <span class="avatar avatar-sm">{{ $user->initials() }}</span>
                                    <span>
                                        <strong>{{ $user->name }}</strong>
                                        @if ($user->is($currentUser))
                                            <small class="cell-sub">Akun Anda</small>
                                        @endif
                                    </span>
                                </span>
                            </td>
                            <td data-label="Email">{{ $user->email }}</td>
                            <td data-label="Role">
                                <span class="badge {{ $user->isAdmin() ? 'badge-admin' : 'badge-officer' }}">
                                    {{ $user->roleLabel() }}
                                </span>
                            </td>
                            <td data-label="Status">
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-muted' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td data-label="Dibuat">{{ $user->created_at?->translatedFormat('d M Y') ?? '—' }}</td>
                            <td data-label="Aksi">
                                <div class="row-actions">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="icon-btn" title="Edit akun" aria-label="Edit akun">
                                        <svg class="icon"><use href="#i-edit"></use></svg>
                                    </a>

                                    @if ($user->is($currentUser))
                                        <button type="button" class="btn btn-sm btn-ghost" disabled title="Tidak dapat menonaktifkan akun sendiri">Akun Anda</button>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" data-confirm="{{ $user->is_active ? 'Nonaktifkan akun ini? Pengguna tidak akan bisa masuk.' : 'Aktifkan kembali akun ini?' }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline' }}">
                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
