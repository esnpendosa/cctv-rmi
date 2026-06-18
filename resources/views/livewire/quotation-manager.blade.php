<div>
    <!-- Title Page & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--color-text-tertiary); font-weight: 700;">Penawaran Harga (Quotations)</h2>
            <p class="text-muted mb-0" style="font-size: var(--font-size-sm);">Buat, kelola, cetak PDF, dan setujui penawaran harga klien untuk instalasi CCTV.</p>
        </div>
        <button type="button" wire:click="create" class="btn btn-primary-custom d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Buat Penawaran
        </button>
    </div>

    <!-- Filters Card -->
    <div class="card-custom border-0">
        <div class="card-custom-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="visually-hidden">Cari Penawaran</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="search" wire:model.live="search" class="form-control form-control-custom border-start-0" placeholder="Cari nomor penawaran, klien, perusahaan, subjek...">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="statusFilter" class="visually-hidden">Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter" class="form-select form-control-custom">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Dikirim (Sent)</option>
                        <option value="accepted">Disetujui (Accepted)</option>
                        <option value="rejected">Ditolak (Rejected)</option>
                        <option value="expired">Kedaluwarsa (Expired)</option>
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
                            <th scope="col">No. Penawaran / Subjek</th>
                            <th scope="col">Klien / Perusahaan</th>
                            <th scope="col">Tanggal / Expired</th>
                            <th scope="col">Subtotal & Total</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $quote)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $quote->number }}</div>
                                    <small class="text-muted">{{ Str::limit($quote->notes, 50) }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $quote->client->name }}</div>
                                    <small class="text-muted">{{ $quote->client->company }}</small>
                                </td>
                                <td>
                                    <div style="font-size: var(--font-size-xs);">Tgl: {{ $quote->created_at->format('d M Y') }}</div>
                                    <div style="font-size: var(--font-size-xs);" class="text-danger">Exp: {{ $quote->valid_until->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <div style="font-size: var(--font-size-xs);">Sub: Rp {{ number_format($quote->subtotal, 0, ',', '.') }}</div>
                                    <div style="font-size: var(--font-size-xs);" class="text-dark fw-bold">Total: Rp {{ number_format($quote->total, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    @if($quote->status->value === 'draft')
                                        <span class="badge-custom badge-secondary">Draft</span>
                                    @elseif($quote->status->value === 'sent')
                                        <span class="badge-custom badge-info">Dikirim</span>
                                    @elseif($quote->status->value === 'accepted')
                                        <span class="badge-custom badge-success">Disetujui</span>
                                    @elseif($quote->status->value === 'rejected')
                                        <span class="badge-custom badge-danger">Ditolak</span>
                                    @else
                                        <span class="badge-custom badge-warning">Expired</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        @if($quote->status->value !== 'accepted')
                                            <button type="button" wire:click="accept({{ $quote->id }})" wire:confirm="Setujui penawaran ini? Ini akan mengurangi stok barang pergudangan dan membuat tagihan/invoice otomatis." class="btn btn-sm btn-outline-success" title="Approve & Convert to Invoice">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </button>
                                        @endif
                                        <a href="{{ route('quotations.pdf', $quote->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Unduh PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        <button type="button" wire:click="edit({{ $quote->id }})" class="btn btn-sm btn-outline-primary" title="Ubah Penawaran">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" wire:click="delete({{ $quote->id }})" wire:confirm="Apakah Anda yakin ingin menghapus penawaran ini beserta itemnya?" class="btn btn-sm btn-outline-danger" title="Hapus Penawaran">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-text d-block mb-3" style="font-size: 3rem;"></i>
                                    Tidak ada data penawaran harga ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-custom-body bg-light border-top p-3">
            {{ $quotations->links() }}
        </div>
    </div>

    <!-- Quotation Form Modal -->
    @if($isFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">
                            {{ $quotationId ? 'Ubah Penawaran Harga' : 'Buat Penawaran Harga Baru' }}
                        </h5>
                        <button type="button" wire:click="closeForm" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                            <!-- Header Form Data -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-dark">Klien / Instansi *</label>
                                    <select wire:model="client_id" class="form-select form-control-custom @error('client_id') is-invalid @enderror">
                                        <option value="">Pilih Klien</option>
                                        @foreach($clients as $c)
                                            <option value="{{ $c->id }}">{{ $c->company }} ({{ $c->name }})</option>
                                        @endforeach
                                    </select>
                                    @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Tanggal Penawaran *</label>
                                    <input type="date" wire:model="date" wire:change="calculateTotals" class="form-control form-control-custom @error('date') is-invalid @enderror">
                                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Tanggal Kedaluwarsa *</label>
                                    <input type="date" wire:model="expires_at" class="form-control form-control-custom @error('expires_at') is-invalid @enderror">
                                    @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Status *</label>
                                    <select wire:model="status" class="form-select form-control-custom @error('status') is-invalid @enderror">
                                        <option value="draft">Draft</option>
                                        <option value="sent">Dikirim (Sent)</option>
                                        <option value="accepted">Disetujui (Accepted)</option>
                                        <option value="rejected">Ditolak (Rejected)</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Quotation Items Grid -->
                            <div class="border rounded p-3 mb-4 bg-light">
                                <h6 class="fw-bold mb-3 text-dark">Item / Jasa Penawaran</h6>
                                
                                @foreach($formItems as $index => $item)
                                    <div class="row g-2 mb-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label text-muted" style="font-size: 11px;">Barang Inventaris *</label>
                                            <select wire:model="formItems.{{ $index }}.inventory_id" wire:change="updateItemPrice({{ $index }})" class="form-select form-control-custom @error('formItems.'.$index.'.inventory_id') is-invalid @enderror">
                                                <option value="">Pilih Barang</option>
                                                @foreach($inventories as $inv)
                                                    <option value="{{ $inv->id }}">{{ $inv->name }} (Merk: {{ $inv->brand }}, Stok: {{ $inv->stock }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label text-muted" style="font-size: 11px;">Jumlah *</label>
                                            <input type="number" wire:model="formItems.{{ $index }}.quantity" wire:change="calculateTotals" class="form-control form-control-custom @error('formItems.'.$index.'.quantity') is-invalid @enderror" min="1">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label text-muted" style="font-size: 11px;">Harga Jual (Rp) *</label>
                                            <input type="number" wire:model="formItems.{{ $index }}.price" wire:change="calculateTotals" class="form-control form-control-custom @error('formItems.'.$index.'.price') is-invalid @enderror" min="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label text-muted" style="font-size: 11px;">Potongan / Diskon (%)</label>
                                            <input type="number" wire:model="formItems.{{ $index }}.discount_percent" wire:change="calculateTotals" class="form-control form-control-custom @error('formItems.'.$index.'.discount_percent') is-invalid @enderror" min="0" max="100">
                                        </div>
                                        <div class="col-md-2 d-flex gap-1 justify-content-end">
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
                                    <label class="form-label fw-semibold text-dark">Catatan Khusus Penawaran</label>
                                    <textarea wire:model="notes" class="form-control form-control-custom" rows="5" placeholder="Syarat pembayaran, metode garansi, waktu instalasi..."></textarea>
                                </div>
                                <div class="col-md-5 bg-light p-3 rounded border">
                                    <h6 class="fw-bold border-bottom pb-2 text-dark">Rincian Total Biaya</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal Item:</span>
                                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Diskon Tambahan (Rp):</span>
                                        <input type="number" wire:model="discount" wire:change="calculateTotals" class="form-control form-control-custom py-1 px-2 text-end" style="width: 150px; font-size: 12px;" min="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>PPN ({{ config('cctv.default_tax_rate', 12) }}%):</span>
                                        <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
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
                            <button type="submit" class="btn btn-primary-custom">Simpan Penawaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
