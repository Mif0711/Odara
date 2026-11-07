@php
    $editing = $item->exists;
    $action = $editing ? route('admin.charger-items.update', $item) : route('admin.charger-items.store');
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
                <h2>Informasi Barang</h2>
                <p>Nama barang akan tampil pada dropdown transaksi Charger.</p>
            </div>
        </div>

        <div class="field">
            <label for="name">Nama Barang <span class="required">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name', $item->name) }}"
                   class="input @error('name') is-invalid @enderror"
                   placeholder="Contoh: Charger HP, Adaptor Laptop, Power Bank" required autofocus>
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="description">Keterangan <span class="optional">(opsional)</span></label>
            <textarea id="description" name="description" rows="3"
                      class="input textarea @error('description') is-invalid @enderror"
                      placeholder="Contoh: Barang titipan pelanggan">{{ old('description', $item->description) }}</textarea>
            @error('description')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <label class="switch-row">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))>
            <span class="switch"></span>
            <span>
                <strong>Barang aktif</strong>
                <small>Barang aktif dapat dipilih pada transaksi Charger baru.</small>
            </span>
        </label>
    </section>

    <div class="form-actions">
        <a href="{{ route('admin.charger-items.index') }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary">
            <svg class="icon"><use href="#i-check"></use></svg>
            {{ $editing ? 'Simpan Perubahan' : 'Simpan Barang' }}
        </button>
    </div>
</form>
