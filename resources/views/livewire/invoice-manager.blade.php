<div>
    <!-- Title Page & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--color-text-tertiary); font-weight: 700;">Invoice Tagihan (Invoices)</h2>
            <p class="text-muted mb-0" style="font-size: var(--font-size-sm);">Kelola invoice, catat pembayaran, pantau piutang, dan status penagihan klien.</p>
        </div>
        <button type="button" wire:click="create" class="btn btn-primary-custom d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Buat Invoice
        </button>
    </div>

    <!-- Filters Card -->
    <div class="card-custom border-0">
        <div class="card-custom-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="visually-hidden">Cari Invoice</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="search" wire:model.live="search" class="form-control form-control-custom border-start-0" placeholder="Cari nomor invoice, nama klien, perusahaan...">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="statusFilter" class="visually-hidden">Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter" class="form-select form-control-custom">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Dikirim (Sent)</option>
                        <option value="paid">Lunas (Paid)</option>
                        <option value="overdue">Jatuh Tempo (Overdue)</option>
                        <option value="cancelled">Dibatalkan (Cancelled)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card-custom border-0 overflow-hidden">
        <div class="card-custom-body p-0">
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th scope="col">No. Invoice</th>
                            <th scope="col">Klien / Perusahaan</th>
                            <th scope="col">Tanggal Penerbitan / Jatuh Tempo</th>
                            <th scope="col">Subtotal & Total</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $invoice->number }}</div>
                                    @if($invoice->quotation)
                                        <small class="text-muted"><i class="bi bi-file-earmark-text"></i> QT: {{ $invoice->quotation->quotation_number }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $invoice->client->name }}</div>
                                    <small class="text-muted">{{ $invoice->client->company }}</small>
                                </td>
                                <td>
                                    <div style="font-size: var(--font-size-xs);">Terbit: {{ $invoice->issue_date->format('d M Y') }}</div>
                                    <div style="font-size: var(--font-size-xs);" class="{{ $invoice->status->value === 'overdue' ? 'text-danger fw-bold' : 'text-muted' }}">Jatuh Tempo: {{ $invoice->due_date->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <div style="font-size: var(--font-size-xs);">Sub: Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</div>
                                    <div style="font-size: var(--font-size-xs);" class="text-dark fw-bold">Total: Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    @if($invoice->status->value === 'draft')
                                        <span class="badge-custom badge-secondary">Draft</span>
                                    @elseif($invoice->status->value === 'sent')
                                        <span class="badge-custom badge-info">Dikirim</span>
                                    @elseif($invoice->status->value === 'paid')
                                        <span class="badge-custom badge-success">Lunas</span>
                                    @elseif($invoice->status->value === 'overdue')
                                        <span class="badge-custom badge-danger">Jatuh Tempo</span>
                                    @else
                                        <span class="badge-custom badge-warning">Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        @if($invoice->status->value !== 'paid' && $invoice->status->value !== 'cancelled')
                                            <button type="button" wire:click="openPaymentModal({{ $invoice->id }})" class="btn btn-sm btn-outline-success" title="Catat Pembayaran">
                                                <i class="bi bi-cash-coin"></i> Bayar
                                            </button>
                                        @endif
                                        <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Unduh / Cetak Invoice">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        <button type="button" wire:click="edit({{ $invoice->id }})" class="btn btn-sm btn-outline-primary" title="Ubah Invoice">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" wire:click="delete({{ $invoice->id }})" wire:confirm="Apakah Anda yakin ingin menghapus invoice ini?" class="btn btn-sm btn-outline-danger" title="Hapus Invoice">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt d-block mb-3" style="font-size: 3rem;"></i>
                                    Tidak ada data invoice ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-custom-body bg-light border-top p-3">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Invoice Form Modal -->
    @if($isFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">
                            {{ $invoiceId ? 'Ubah Invoice' : 'Buat Invoice Baru' }}
                        </h5>
                        <button type="button" wire:click="closeForm" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                            <!-- Header Form Data -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Klien / Instansi *</label>
                                    <select wire:model="client_id" class="form-select form-control-custom @error('client_id') is-invalid @enderror">
                                        <option value="">Pilih Klien</option>
                                        @foreach($clients as $c)
                                            <option value="{{ $c->id }}">{{ $c->company }} ({{ $c->name }})</option>
                                        @endforeach
                                    </select>
                                    @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Status *</label>
                                    <select wire:model="status" class="form-select form-control-custom @error('status') is-invalid @enderror">
                                        <option value="draft">Draft</option>
                                        <option value="sent">Dikirim (Sent)</option>
                                        <option value="paid">Lunas (Paid)</option>
                                        <option value="overdue">Jatuh Tempo (Overdue)</option>
                                        <option value="cancelled">Dibatalkan (Cancelled)</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Tanggal Penerbitan *</label>
                                    <input type="date" wire:model="issue_date" wire:change="calculateTotals" class="form-control form-control-custom @error('issue_date') is-invalid @enderror">
                                    @error('issue_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Tanggal Jatuh Tempo *</label>
                                    <input type="date" wire:model="due_date" class="form-control form-control-custom @error('due_date') is-invalid @enderror">
                                    @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Invoice Items Grid -->
                            <div class="border rounded p-3 mb-4 bg-light">
                                <h6 class="fw-bold mb-3 text-dark">Item / Deskripsi Tagihan</h6>
                                
                                @foreach($formItems as $index => $item)
                                    <div class="row g-2 mb-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label text-muted" style="font-size: 11px;">Barang Inventaris (Opsional)</label>
                                            <select wire:model="formItems.{{ $index }}.inventory_id" wire:change="updateItemPrice({{ $index }})" class="form-select form-control-custom">
                                                <option value="">Pilih Barang (Atau isi manual)</option>
                                                @foreach($inventories as $inv)
                                                    <option value="{{ $inv->id }}">{{ $inv->name }} (Merk: {{ $inv->brand }}, Stok: {{ $inv->stock }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-muted" style="font-size: 11px;">Deskripsi / Nama Item *</label>
                                            <input type="text" wire:model="formItems.{{ $index }}.description" class="form-control form-control-custom @error('formItems.'.$index.'.description') is-invalid @enderror" placeholder="Pemasangan kabel UTP, camera, etc">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label text-muted" style="font-size: 11px;">Qty *</label>
                                            <input type="number" wire:model="formItems.{{ $index }}.qty" wire:change="calculateTotals" class="form-control form-control-custom @error('formItems.'.$index.'.qty') is-invalid @enderror" min="1">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label text-muted" style="font-size: 11px;">Harga Satuan (Rp) *</label>
                                            <input type="number" wire:model="formItems.{{ $index }}.unit_price" wire:change="calculateTotals" class="form-control form-control-custom @error('formItems.'.$index.'.unit_price') is-invalid @enderror" min="0">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label text-muted" style="font-size: 11px;">Diskon (%)</label>
                                            <input type="number" wire:model="formItems.{{ $index }}.discount_percent" wire:change="calculateTotals" class="form-control form-control-custom @error('formItems.'.$index.'.discount_percent') is-invalid @enderror" min="0" max="100">
                                        </div>
                                        <div class="col-md-1 d-flex justify-content-end">
                                            <button type="button" wire:click="removeFormItem({{ $index }})" class="btn btn-outline-danger w-100" title="Hapus Item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach

                                <button type="button" wire:click="addFormItem" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> Tambah Item Lain
                                </button>
                            </div>

                            <!-- Financial Recalculations Section -->
                            <div class="row">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold text-dark">Catatan Invoice</label>
                                    <textarea wire:model="notes" class="form-control form-control-custom" rows="5" placeholder="Syarat pembayaran, metode garansi, waktu instalasi..."></textarea>
                                </div>
                                <div class="col-md-5 bg-light p-3 rounded border">
                                    <h6 class="fw-bold border-bottom pb-2 text-dark">Rincian Total Biaya</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal Item:</span>
                                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Potongan Harga Langsung (Rp):</span>
                                        <input type="number" wire:model="discount_amount" wire:change="calculateTotals" class="form-control form-control-custom py-1 px-2 text-end" style="width: 150px; font-size: 12px;" min="0">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>PPN (%):</span>
                                        <input type="number" wire:model="tax_percent" wire:change="calculateTotals" class="form-control form-control-custom py-1 px-2 text-end" style="width: 80px; font-size: 12px;" min="0" max="100">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Jumlah PPN:</span>
                                        <span>Rp {{ number_format($tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-0 fw-bold fs-5 text-dark">
                                        <span>Grand Total:</span>
                                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" wire:click="closeForm" class="btn btn-secondary-custom">Batal</button>
                            <button type="submit" class="btn btn-primary-custom">Simpan Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Modal -->
    @if($isPaymentModalOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">Catat Pembayaran Invoice</h5>
                        <button type="button" wire:click="closePaymentModal" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="recordPayment">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Metode Pembayaran *</label>
                                <select wire:model="payment_method" class="form-select form-control-custom @error('payment_method') is-invalid @enderror">
                                    <option value="Cash">Cash / Tunai</option>
                                    <option value="Bank Transfer">Bank Transfer / Transfer Bank</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="E-Wallet">E-Wallet (OVO/GoPay/Dana)</option>
                                </select>
                                @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Bukti Pembayaran / ID Referensi (Opsional)</label>
                                <input type="text" wire:model="payment_proof" class="form-control form-control-custom @error('payment_proof') is-invalid @enderror" placeholder="Contoh: No. Ref TRX-100293">
                                @error('payment_proof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" wire:click="closePaymentModal" class="btn btn-secondary-custom">Batal</button>
                            <button type="submit" class="btn btn-success-custom bg-success text-white border-0 py-2 px-3 fw-semibold rounded" style="box-shadow: var(--shadow-2);">Konfirmasi Lunas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
