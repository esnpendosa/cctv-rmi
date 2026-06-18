<div>
    <!-- Title Page & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--color-text-tertiary); font-weight: 700;">Inventaris & Stok Barang</h2>
            <p class="text-muted mb-0" style="font-size: var(--font-size-sm);">Kelola barang pergudangan, tingkat ketersediaan stok, nilai aset, dan mutasi barang.</p>
        </div>
        @can('inventory.create')
            <button type="button" wire:click="create" class="btn btn-primary-custom d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </button>
        @endcan
    </div>

    <!-- Filters Card -->
    <div class="card-custom border-0">
        <div class="card-custom-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="visually-hidden">Cari Barang</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="search" wire:model.live="search" class="form-control form-control-custom border-start-0" placeholder="Cari SKU, nama barang, merk...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="conditionFilter" class="visually-hidden">Kondisi</label>
                    <select id="conditionFilter" wire:model.live="conditionFilter" class="form-select form-control-custom">
                        <option value="">Semua Kondisi</option>
                        <option value="new">Baru (New)</option>
                        <option value="used">Bekas (Used)</option>
                        <option value="broken">Rusak (Broken)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="locationFilter" class="visually-hidden">Gudang / Lokasi</label>
                    <select id="locationFilter" wire:model.live="locationFilter" class="form-select form-control-custom">
                        <option value="">Semua Gudang / Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
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
                            <th scope="col">SKU / Nama Barang</th>
                            <th scope="col">Merk & Tipe</th>
                            <th scope="col">Harga Beli / Jual</th>
                            <th scope="col">Kondisi</th>
                            <th scope="col">Gudang</th>
                            <th scope="col">Stok / Minimal</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->name }}</div>
                                    <small class="text-muted">SKU: <code>{{ $item->sku }}</code></small>
                                </td>
                                <td>{{ $item->brand }} / {{ $item->model }}</td>
                                <td>
                                    <div style="font-size: var(--font-size-xs);">Beli: Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</div>
                                    <div style="font-size: var(--font-size-xs);" class="text-dark fw-semibold">Jual: Rp {{ number_format($item->selling_price, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    @if($item->condition->value === 'new')
                                        <span class="badge-custom badge-success">Baru</span>
                                    @elseif($item->condition->value === 'used')
                                        <span class="badge-custom badge-info">Bekas</span>
                                    @else
                                        <span class="badge-custom badge-danger">Rusak</span>
                                    @endif
                                </td>
                                <td>{{ $item->location ? $item->location->name : 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold fs-6 {{ $item->stock <= $item->min_stock ? 'text-danger' : 'text-dark' }}">{{ $item->stock }}</span>
                                        <small class="text-muted">/ {{ $item->min_stock }}</small>
                                        @if($item->stock <= $item->min_stock)
                                            <span class="badge bg-danger" style="font-size: 0.65rem;">LOW</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        @can('inventory.update')
                                            <button type="button" wire:click="openAdjust({{ $item->id }})" class="btn btn-sm btn-outline-info" title="Adjust Stock">
                                                <i class="bi bi-arrow-down-up"></i>
                                            </button>
                                            <button type="button" wire:click="edit({{ $item->id }})" class="btn btn-sm btn-outline-primary" title="Edit Item">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan
                                        @can('inventory.delete')
                                            <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus barang ini?" class="btn btn-sm btn-outline-danger" title="Delete Item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam d-block mb-3" style="font-size: 3rem;"></i>
                                    Tidak ada data barang inventaris ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-custom-body bg-light border-top p-3">
            {{ $items->links() }}
        </div>
    </div>

    <!-- Inventory Form Modal -->
    @if($isFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">
                            {{ $inventoryId ? 'Ubah Barang Inventaris' : 'Tambah Barang Inventaris Baru' }}
                        </h5>
                        <button type="button" wire:click="closeForm" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">SKU Barang *</label>
                                    <input type="text" wire:model="sku" class="form-control form-control-custom @error('sku') is-invalid @enderror" placeholder="CAM-HIK-DS2CD">
                                    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-dark">Nama Barang *</label>
                                    <input type="text" wire:model="name" class="form-control form-control-custom @error('name') is-invalid @enderror" placeholder="Kamera Dome IP 2MP">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Kategori Barang *</label>
                                    <input type="text" wire:model="category" class="form-control form-control-custom @error('category') is-invalid @enderror" placeholder="Kamera, NVR, Kabel, Aksesoris...">
                                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Satuan (Unit) *</label>
                                    <input type="text" wire:model="unit" class="form-control form-control-custom @error('unit') is-invalid @enderror" placeholder="pcs, meter, unit...">
                                    @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Merk / Produsen *</label>
                                    <input type="text" wire:model="brand" class="form-control form-control-custom @error('brand') is-invalid @enderror" placeholder="Hikvision">
                                    @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Tipe / Model *</label>
                                    <input type="text" wire:model="model" class="form-control form-control-custom @error('model') is-invalid @enderror" placeholder="DS-2CD2021G1-I">
                                    @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Harga Beli (Rp) *</label>
                                    <input type="number" wire:model="purchase_price" class="form-control form-control-custom @error('purchase_price') is-invalid @enderror" placeholder="500000">
                                    @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Harga Jual (Rp) *</label>
                                    <input type="number" wire:model="selling_price" class="form-control form-control-custom @error('selling_price') is-invalid @enderror" placeholder="750000">
                                    @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Jumlah Stok Awal *</label>
                                    <input type="number" wire:model="stock" class="form-control form-control-custom @error('stock') is-invalid @enderror" placeholder="20" {{ $inventoryId ? 'disabled' : '' }}>
                                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Tingkat Peringatan Minimal *</label>
                                    <input type="number" wire:model="min_stock" class="form-control form-control-custom @error('min_stock') is-invalid @enderror" placeholder="5">
                                    @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Kondisi Barang *</label>
                                    <select wire:model="condition" class="form-select form-control-custom @error('condition') is-invalid @enderror">
                                        <option value="new">Baru (New)</option>
                                        <option value="used">Bekas (Used)</option>
                                        <option value="broken">Rusak (Broken)</option>
                                    </select>
                                    @error('condition') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-dark">Gudang / Lokasi Penyimpanan *</label>
                                    <select wire:model="location_id" class="form-select form-control-custom @error('location_id') is-invalid @enderror">
                                        <option value="">Pilih Gudang / Lokasi</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-dark">Catatan / Deskripsi Tambahan</label>
                                    <textarea wire:model="notes" class="form-control form-control-custom @error('notes') is-invalid @enderror" rows="2" placeholder="Catatan spesifikasi detail barang..."></textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" wire:click="closeForm" class="btn btn-secondary-custom">Batal</button>
                            <button type="submit" class="btn btn-primary-custom">Simpan Barang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Stock Adjustment Modal -->
    @if($isAdjustOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">Penyesuaian Stok (Stock Mutation)</h5>
                        <button type="button" wire:click="closeAdjust" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveAdjustment">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <strong>Nama Barang:</strong> {{ $adjustingItem->name }}<br>
                                <strong>Stok Saat Ini:</strong> <span class="fw-bold">{{ $adjustingItem->stock }} unit</span>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Jumlah Perubahan Stok *</label>
                                <input type="number" wire:model="adjustQuantity" class="form-control form-control-custom @error('adjustQuantity') is-invalid @enderror" placeholder="Contoh: 10 untuk tambah, -5 untuk kurang">
                                <small class="text-muted">Gunakan angka positif untuk barang masuk, negatif untuk barang keluar.</small>
                                @error('adjustQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Alasan Perubahan *</label>
                                <input type="text" wire:model="adjustReason" class="form-control form-control-custom @error('adjustReason') is-invalid @enderror" placeholder="Contoh: Penjualan Project, Barang Rusak, Restock Gudang">
                                @error('adjustReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold text-dark">Catatan Tambahan (Opsional)</label>
                                <textarea wire:model="adjustNotes" class="form-control form-control-custom" rows="2" placeholder="Catatan mutasi stok tambahan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" wire:click="closeAdjust" class="btn btn-secondary-custom">Batal</button>
                            <button type="submit" class="btn btn-primary-custom">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
