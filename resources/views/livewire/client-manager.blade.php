<div>
    <!-- Title Page & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--color-text-tertiary); font-weight: 700;">Daftar Klien & Lokasi</h2>
            <p class="text-muted mb-0" style="font-size: var(--font-size-sm);">Kelola data perusahaan klien beserta lokasi titik pemasangan CCTV.</p>
        </div>
        <button type="button" wire:click="createClient" class="btn btn-primary-custom d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Tambah Klien
        </button>
    </div>

    <div class="row">
        <!-- Client Table Section -->
        <div class="{{ $activeClientIdForLocations ? 'col-lg-7' : 'col-lg-12' }} transition-all" style="transition: width var(--motion-duration-normal) ease;">
            <!-- Filters Card -->
            <div class="card-custom border-0">
                <div class="card-custom-body">
                    <label for="search" class="visually-hidden">Cari Klien</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="search" wire:model.live="search" class="form-control form-control-custom border-start-0" placeholder="Cari nama klien, instansi, email...">
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
                                    <th scope="col">Nama Kontak</th>
                                    <th scope="col">Perusahaan</th>
                                    <th scope="col">Email & Telepon</th>
                                    <th scope="col">Jumlah Titik</th>
                                    <th scope="col" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr class="{{ $activeClientIdForLocations === $client->id ? 'table-primary-subtle' : '' }}" style="cursor: pointer;" wire:click="viewLocations({{ $client->id }})">
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $client->name }}</div>
                                            <span style="font-size: var(--font-size-xs); color: var(--color-text-primary);">{{ Str::limit($client->address, 30) }}</span>
                                        </td>
                                        <td><span class="fw-bold" style="color: var(--color-text-tertiary);">{{ $client->company }}</span></td>
                                        <td>
                                            <div style="font-size: var(--font-size-xs);">{{ $client->email }}</div>
                                            <div style="font-size: var(--font-size-xs);" class="text-muted">{{ $client->phone }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary" style="border-radius: var(--radius-xs);">
                                                {{ $client->locations_count }} Lokasi
                                            </span>
                                        </td>
                                        <td class="text-end" wire:click.stop>
                                            <div class="d-inline-flex gap-2">
                                                <button type="button" wire:click="viewLocations({{ $client->id }})" class="btn btn-sm btn-outline-info" title="Manage Locations">
                                                    <i class="bi bi-geo-alt"></i>
                                                </button>
                                                <button type="button" wire:click="editClient({{ $client->id }})" class="btn btn-sm btn-outline-primary" title="Edit Client">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" wire:click="deleteClient({{ $client->id }})" wire:confirm="Apakah Anda yakin ingin menghapus klien ini?" class="btn btn-sm btn-outline-danger" title="Delete Client">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-people d-block mb-3" style="font-size: 3rem;"></i>
                                            Tidak ada data klien ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-custom-body bg-light border-top p-3">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>

        <!-- Locations Details Drawer (Right Panel) -->
        @if($activeClientIdForLocations)
            <div class="col-lg-5">
                <div class="card-custom border-0">
                    <div class="card-custom-header">
                        <div>
                            <span class="fw-bold" style="color: var(--color-text-tertiary);">Lokasi: {{ $activeClient->company }}</span>
                            <small class="text-muted d-block" style="font-size: 11px;">Kelola titik instalasi untuk klien ini.</small>
                        </div>
                        <button type="button" wire:click="createLocation({{ $activeClient->id }})" class="btn btn-sm btn-primary-custom d-flex align-items-center gap-1">
                            <i class="bi bi-plus-lg"></i> Tambah Lokasi
                        </button>
                    </div>
                    <div class="card-custom-body p-0" style="max-height: 500px; overflow-y: auto;">
                        @if($activeClientLocations->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-geo d-block mb-2 text-secondary" style="font-size: 2rem;"></i>
                                Belum ada lokasi terdaftar untuk klien ini.
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($activeClientLocations as $loc)
                                    <div class="list-group-item p-3 border-0 border-bottom d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-3">
                                            <h6 class="mb-1 text-dark fw-bold" style="font-size: var(--font-size-sm);">{{ $loc->name }}</h6>
                                            <p class="mb-1 text-secondary" style="font-size: var(--font-size-xs); line-height: 1.4;">{{ $loc->address }}</p>
                                            @if($loc->latitude && $loc->longitude)
                                                <small class="text-muted d-block" style="font-size: 11px;">
                                                    <i class="bi bi-globe"></i> GPS: {{ $loc->latitude }}, {{ $loc->longitude }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" wire:click="editLocation({{ $loc->id }})" class="btn btn-xs btn-outline-primary p-1" title="Edit Location">
                                                <i class="bi bi-pencil" style="font-size: 0.85rem;"></i>
                                            </button>
                                            <button type="button" wire:click="deleteLocation({{ $loc->id }})" wire:confirm="Yakin ingin menghapus lokasi ini?" class="btn btn-xs btn-outline-danger p-1" title="Delete Location">
                                                <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Client Form Modal -->
    @if($isClientFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">
                            {{ $clientId ? 'Ubah Kontak Klien' : 'Tambah Klien Baru' }}
                        </h5>
                        <button type="button" wire:click="closeClientForm" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveClient">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Nama Kontak Utama *</label>
                                <input type="text" wire:model="clientName" class="form-control form-control-custom @error('clientName') is-invalid @enderror" placeholder="Budi Santoso">
                                @error('clientName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Nama Instansi / Perusahaan *</label>
                                <input type="text" wire:model="clientCompany" class="form-control form-control-custom @error('clientCompany') is-invalid @enderror" placeholder="PT. Sukses Mulia">
                                @error('clientCompany') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Email Address *</label>
                                    <input type="email" wire:model="clientEmail" class="form-control form-control-custom @error('clientEmail') is-invalid @enderror" placeholder="budi@suksesmulia.com">
                                    @error('clientEmail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">No. Telepon / WA *</label>
                                    <input type="text" wire:model="clientPhone" class="form-control form-control-custom @error('clientPhone') is-invalid @enderror" placeholder="081234567890">
                                    @error('clientPhone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold text-dark">Alamat Perusahaan *</label>
                                <textarea wire:model="clientAddress" class="form-control form-control-custom @error('clientAddress') is-invalid @enderror" rows="3" placeholder="Jl. Sudirman No. 10, Jakarta Selatan"></textarea>
                                @error('clientAddress') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" wire:click="closeClientForm" class="btn btn-secondary-custom">Batal</button>
                            <button type="submit" class="btn btn-primary-custom">Simpan Klien</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Location Form Modal -->
    @if($isLocationFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">
                            {{ $locationId ? 'Ubah Titik Lokasi' : 'Tambah Lokasi Baru' }}
                        </h5>
                        <button type="button" wire:click="closeLocationForm" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="saveLocation">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Nama Lokasi / Area *</label>
                                <input type="text" wire:model="locationName" class="form-control form-control-custom @error('locationName') is-invalid @enderror" placeholder="Kantor Cabang / Pos Satpam">
                                @error('locationName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Alamat Lengkap Lokasi *</label>
                                <textarea wire:model="locationAddress" class="form-control form-control-custom @error('locationAddress') is-invalid @enderror" rows="3" placeholder="Alamat lengkap lokasi titik CCTV..."></textarea>
                                @error('locationAddress') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Latitude (GPS)</label>
                                    <input type="text" wire:model="locationLatitude" class="form-control form-control-custom @error('locationLatitude') is-invalid @enderror" placeholder="-6.2088">
                                    @error('locationLatitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Longitude (GPS)</label>
                                    <input type="text" wire:model="locationLongitude" class="form-control form-control-custom @error('locationLongitude') is-invalid @enderror" placeholder="106.8456">
                                    @error('locationLongitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" wire:click="closeLocationForm" class="btn btn-secondary-custom">Batal</button>
                            <button type="submit" class="btn btn-primary-custom">Simpan Lokasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
