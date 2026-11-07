@php
    $editing = $room->exists;
    $action = $editing ? route('admin.rooms.update', $room) : route('admin.rooms.store');
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
                <h2>Informasi Kamar</h2>
                <p>Nama kamar akan tampil pada dropdown formulir transaksi.</p>
            </div>
        </div>

        <div class="field">
            <label for="name">Nama Kamar <span class="required">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name', $room->name) }}"
                   class="input @error('name') is-invalid @enderror"
                   placeholder="Contoh: Kamar A1, Rak 2, Loker 3" required autofocus>
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="description">Keterangan <span class="optional">(opsional)</span></label>
            <textarea id="description" name="description" rows="3"
                      class="input textarea @error('description') is-invalid @enderror"
                      placeholder="Contoh: Kamar khusus barang besar">{{ old('description', $room->description) }}</textarea>
            @error('description')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <label class="switch-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $room->is_active))>
            <span class="switch"></span>
            <span>
                <strong>Kamar aktif</strong>
                <small>Kamar aktif dapat dipilih pada transaksi baru.</small>
            </span>
        </label>
    </section>

    <div class="form-actions">
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary">
            <svg class="icon"><use href="#i-check"></use></svg>
            {{ $editing ? 'Simpan Perubahan' : 'Simpan Kamar' }}
        </button>
    </div>
</form>
