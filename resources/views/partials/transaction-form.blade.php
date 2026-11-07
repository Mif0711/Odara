@php
    $isCharger = $type === 'charger';
    $oldRoom = old('room_id', $transaction?->room_id);
    $dateValue = old('transaction_date', $transaction?->transaction_date?->format('Y-m-d') ?? now()->toDateString());
    $hasRooms = $rooms->isNotEmpty();
    $items = $items ?? collect();
    $hasItems = $items->isNotEmpty();
    $oldItem = old('charger_item_id', $transaction?->charger_item_id);
    $oldPin = old('pin_number', $transaction?->pin_number);
    $pinOptions = range(1, 30);
    $isPickedUp = (bool) old('is_picked_up', $transaction?->is_picked_up ?? false);
    $unitPrice = (float) old('item_price', $transaction?->item_price ?? 0);
    $serviceFee = (float) old('service_fee', $transaction?->service_fee ?? 0);
    $qty = (int) old('qty', $transaction?->qty ?? 1);
    $total = $unitPrice + $serviceFee;
    $canSubmit = $hasRooms && (! $isCharger || $hasItems);
@endphp

<form method="POST" action="{{ $action }}" class="transaction-form" data-calc-form>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <section class="form-section">
        <div class="form-section-head">
            <div>
                <h2>Informasi Pelanggan</h2>
                <p>
                    @if ($isCharger)
                        Ketik nama pelanggan, lalu pilih barang Charger dari daftar yang tersedia.
                    @else
                        Ketik nama pelanggan dan jenis barang secara manual.
                    @endif
                </p>
            </div>
        </div>

        <div class="form-grid form-grid-2">
            <div class="field">
                <label for="customer_name">Nama Pelanggan <span class="required">*</span></label>
                <input id="customer_name" type="text" name="customer_name"
                       value="{{ old('customer_name', $transaction?->customer_name) }}"
                       class="input @error('customer_name') is-invalid @enderror"
                       list="customer-name-options" placeholder="Ketik nama pelanggan"
                       autocomplete="off" required>
                <datalist id="customer-name-options">
                    @foreach ($customerNames as $name)
                        <option value="{{ $name }}"></option>
                    @endforeach
                </datalist>
                <span class="field-hint">Autocomplete muncul dari riwayat transaksi Anda.</span>
                @error('customer_name')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            @if ($isCharger)
                <div class="field">
                    <label for="charger_item_id">Barang Charger <span class="required">*</span></label>
                    <select id="charger_item_id" name="charger_item_id"
                            class="select @error('charger_item_id') is-invalid @enderror"
                            required @disabled(! $hasItems)>
                        <option value="">Pilih barang</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected((string) $oldItem === (string) $item->id)>
                                {{ $item->name }}{{ ! $item->is_active ? ' (Nonaktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if (! $hasItems)
                        <span class="field-error">Belum ada barang Charger aktif. Hubungi Admin untuk menambahkan data barang.</span>
                    @else
                        <span class="field-hint">Barang dikelola oleh Admin melalui menu Data Barang Charger.</span>
                    @endif
                    @error('charger_item_id')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            @else
                <div class="field">
                    <label for="item_type">Jenis Barang <span class="required">*</span></label>
                    <input id="item_type" type="text" name="item_type"
                           value="{{ old('item_type', $transaction?->item_type) }}"
                           class="input @error('item_type') is-invalid @enderror"
                           placeholder="Contoh: Pakaian, Sepatu, Elektronik" autocomplete="off" required>
                    <span class="field-hint">Diketik manual, bukan pilihan dropdown.</span>
                    @error('item_type')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>

        <div class="form-grid form-grid-2">
            <div class="field">
                <label for="room_id">Kamar <span class="required">*</span></label>
                <select id="room_id" name="room_id" class="select @error('room_id') is-invalid @enderror" required @disabled(! $hasRooms)>
                    <option value="">Pilih kamar</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected((string) $oldRoom === (string) $room->id)>
                            {{ $room->name }}{{ ! $room->is_active ? ' (Nonaktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @if (! $hasRooms)
                    <span class="field-error">Belum ada kamar aktif. Hubungi Admin untuk menambahkan data kamar.</span>
                @else
                    <span class="field-hint">Hanya kamar aktif yang dapat dipilih untuk transaksi baru.</span>
                @endif
                @error('room_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="transaction_date">Tanggal <span class="required">*</span></label>
                <input id="transaction_date" type="date" name="transaction_date"
                       value="{{ $dateValue }}"
                       class="input @error('transaction_date') is-invalid @enderror" required>
                @error('transaction_date')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="auto-field">
            <span class="avatar avatar-sm">{{ auth()->user()->initials() }}</span>
            <div>
                <span class="auto-label">Petugas pencatat</span>
                <strong>{{ auth()->user()->name }}</strong>
            </div>
            <span class="badge badge-soft">Otomatis</span>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-head">
            <div>
                <h2>Detail Transaksi</h2>
                <p>Lengkapi data transaksi {{ $isCharger ? 'Charger' : 'Shopee' }} di bawah ini.</p>
            </div>
        </div>

        <div class="form-grid {{ $isCharger ? 'form-grid-2' : 'form-grid-1' }}">
            @if ($isCharger)
                <div class="field">
                    <label for="pin_number">Nomor PIN <span class="required">*</span></label>
                    <select id="pin_number" name="pin_number"
                            class="select @error('pin_number') is-invalid @enderror" required>
                        <option value="">Pilih nomor PIN</option>
                        @foreach ($pinOptions as $pin)
                            <option value="{{ $pin }}" @selected((string) $oldPin === (string) $pin)>{{ $pin }}</option>
                        @endforeach
                    </select>
                    <span class="field-hint">Pilih nomor PIN charger antara 1 sampai 30.</span>
                    @error('pin_number')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            @else
                <div class="field">
                    <label for="receipt_number">
                        Nomor Resi
                        <span class="optional">(opsional)</span>
                    </label>
                    <input id="receipt_number"
                           type="text"
                           name="receipt_number"
                           value="{{ old('receipt_number', $transaction?->receipt_number) }}"
                           class="input @error('receipt_number') is-invalid @enderror"
                           placeholder="Nomor resi pengiriman"
                           autocomplete="off">
                    @error('receipt_number')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            @if ($isCharger)
                <div class="field">
                    <label for="qty">QTY <span class="required">*</span></label>
                    <input id="qty" type="number" name="qty" value="{{ $qty }}"
                           class="input @error('qty') is-invalid @enderror"
                           min="1" step="1" inputmode="numeric" required data-qty>
                    @error('qty')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>

        @unless ($isCharger)
            <div class="form-grid form-grid-2">
                <div class="field">
                    <label for="item_price">Harga Barang <span class="required">*</span></label>
                    <div class="input-prefix">
                        <span>Rp</span>
                        <input id="item_price" type="text" name="item_price" value="{{ \App\Support\Money::numeric($unitPrice) }}"
                               class="input @error('item_price') is-invalid @enderror"
                               inputmode="numeric" autocomplete="off" required data-price>
                    </div>
                    @error('item_price')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="service_fee">Jasa <span class="required">*</span></label>
                    <div class="input-prefix">
                        <span>Rp</span>
                        <input id="service_fee" type="text" name="service_fee" value="{{ \App\Support\Money::numeric($serviceFee) }}"
                               class="input @error('service_fee') is-invalid @enderror"
                               inputmode="numeric" autocomplete="off" required data-service>
                    </div>
                    @error('service_fee')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @endunless

        <label class="switch-row">
            <input type="checkbox" name="is_picked_up" value="1" @checked($isPickedUp)>
            <span class="switch"></span>
            <span>
                <strong>Barang sudah diambil</strong>
                <small>Aktifkan jika barang sudah diserahkan kembali kepada pelanggan.</small>
            </span>
        </label>

        <div class="field">
            <label for="notes">Keterangan <span class="optional">(opsional)</span></label>
            <textarea id="notes" name="notes" rows="3"
                      class="input textarea @error('notes') is-invalid @enderror"
                      placeholder="Tambahkan catatan bila diperlukan">{{ old('notes', $transaction?->notes) }}</textarea>
            @error('notes')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        @unless ($isCharger)
            <div class="total-preview">
                <span>Estimasi total <small>(Harga + Jasa)</small></span>
                <strong data-total-output>{{ \App\Support\Money::idr($total) }}</strong>
            </div>
        @endunless
    </section>

    <div class="form-actions">
        <a href="{{ $cancelUrl }}" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary btn-lg" @disabled(! $canSubmit)>
            <svg class="icon"><use href="#i-check"></use></svg>
            {{ $submitLabel }}
        </button>
    </div>
</form>
