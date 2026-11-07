@php
    $editing = $account->exists;
    $isSelf = $editing && isset($currentUser) && $account->is($currentUser);
    $action = $editing ? route('admin.users.update', $account) : route('admin.users.store');
    $method = $editing ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" class="form-card">
    @csrf
    @if ($editing)
        @method($method)
    @endif

    <section class="form-section">
        <div class="form-section-head">
            <div>
                <h2>Informasi Akun</h2>
                <p>Akun ini digunakan untuk masuk ke aplikasi ODARA.</p>
            </div>
        </div>

        <div class="form-grid form-grid-2">
            <div class="field">
                <label for="name">Nama Lengkap <span class="required">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $account->name) }}"
                       class="input @error('name') is-invalid @enderror"
                       placeholder="Nama lengkap pengguna" required autofocus autocomplete="name">
                @error('name')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="email">Email <span class="required">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $account->email) }}"
                       class="input @error('email') is-invalid @enderror"
                       placeholder="nama@odara.id" required autocomplete="email">
                @error('email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-grid form-grid-2">
            <div class="field">
                <label for="role">Role <span class="required">*</span></label>
                @if ($isSelf)
                    <input type="hidden" name="role" value="{{ $account->role }}">
                    <select id="role" class="select" disabled>
                        <option>{{ $account->roleLabel() }}</option>
                    </select>
                    <span class="field-hint">Role akun sendiri tidak dapat diubah.</span>
                @else
                    <select id="role" name="role" class="select @error('role') is-invalid @enderror" required>
                        <option value="petugas" @selected(old('role', $account->role) === 'petugas')>Petugas</option>
                        <option value="admin" @selected(old('role', $account->role) === 'admin')>Admin</option>
                    </select>
                    <span class="field-hint">Admin memiliki akses penuh, Petugas mencatat transaksi.</span>
                    @error('role')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                @endif
            </div>

            <div class="field">
                <label for="password">
                    {{ $editing ? 'Password Baru' : 'Password' }}
                    @if ($editing)
                        <span class="optional">(opsional)</span>
                    @else
                        <span class="required">*</span>
                    @endif
                </label>
                <input id="password" type="password" name="password"
                       class="input @error('password') is-invalid @enderror"
                       placeholder="{{ $editing ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                       autocomplete="new-password" {{ $editing ? '' : 'required' }}>
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="field">
            <label for="password_confirmation">Ulangi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="input" placeholder="{{ $editing ? 'Ulangi password baru' : 'Ulangi password' }}"
                   autocomplete="new-password">
        </div>

        @if ($isSelf)
            <input type="hidden" name="is_active" value="1">
            <div class="switch-row switch-row-disabled">
                <input type="checkbox" checked disabled>
                <span class="switch"></span>
                <span>
                    <strong>Akun aktif</strong>
                    <small>Status akun sendiri tidak dapat diubah.</small>
                </span>
            </div>
        @else
            <label class="switch-row">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active))>
                <span class="switch"></span>
                <span>
                    <strong>Akun aktif</strong>
                    <small>Akun nonaktif tidak dapat masuk ke aplikasi.</small>
                </span>
            </label>
        @endif

        @error('is_active')
            <span class="field-error">{{ $message }}</span>
        @enderror
    </section>

    <div class="form-actions">
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary">
            <svg class="icon"><use href="#i-check"></use></svg>
            {{ $editing ? 'Simpan Perubahan' : 'Buat Akun' }}
        </button>
    </div>
</form>
