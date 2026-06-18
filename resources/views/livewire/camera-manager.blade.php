<div>
    <!-- Title Page & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-header mb-0">
            <h2 class="mb-0">Manajemen Kamera CCTV</h2>
            <p class="page-subtitle mb-0">Kelola kamera, RTSP stream, lokasi, dan hak akses sharing publik.</p>
        </div>
        @can('camera.create')
            <button type="button" wire:click="create" class="btn btn-primary-custom d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> Tambah Kamera
            </button>
        @endcan
    </div>

    <!-- Filters Card -->
    <div class="card-custom border-0">
        <div class="card-custom-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label for="search" class="visually-hidden">Cari Kamera</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="search" wire:model.live="search" class="form-control form-control-custom border-start-0" placeholder="Cari nama, IP, model...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="visually-hidden">Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter" class="form-select form-control-custom">
                        <option value="">Semua Status</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="locationFilter" class="visually-hidden">Lokasi</label>
                    <select id="locationFilter" wire:model.live="locationFilter" class="form-select form-control-custom">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="categoryFilter" class="visually-hidden">Kategori</label>
                    <select id="categoryFilter" wire:model.live="categoryFilter" class="form-select form-control-custom">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                        <span class="text-secondary d-none d-lg-inline" style="font-size: var(--font-size-xs); font-weight: 600; text-transform: uppercase;">Tampilan:</span>
                        <div class="btn-group shadow-sm" role="group" style="border-radius: var(--radius-xs); overflow: hidden;">
                            <button type="button" wire:click="$set('viewMode', 'grid')" class="btn btn-sm {{ $viewMode === 'grid' ? 'btn-primary-custom' : 'btn-secondary-custom' }} d-flex align-items-center gap-1" style="padding: 6px 12px;">
                                <i class="bi bi-grid-fill"></i> Grid
                            </button>
                            <button type="button" wire:click="$set('viewMode', 'table')" class="btn btn-sm {{ $viewMode === 'table' ? 'btn-primary-custom' : 'btn-secondary-custom' }} d-flex align-items-center gap-1" style="padding: 6px 12px;">
                                <i class="bi bi-list-task"></i> Tabel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($viewMode === 'grid')
        <!-- Grid View -->
        <div class="row g-4 mb-4">
            @forelse($cameras as $camera)
                <div class="col-xl-4 col-md-6">
                    <div class="card-custom border-0 h-100 overflow-hidden" style="margin-bottom: 0;">
                        <!-- Video Stream Placeholder / Mock Feed -->
                        <div class="position-relative bg-dark d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(rgba(0,0,0,0.15), rgba(0,0,0,0.5)), #111;">
                            
                            <!-- Static Grid lines or Scanner scan lines simulated via CSS overlay -->
                            <div class="position-absolute w-100 h-100" style="background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(30, 144, 255, 0.05), rgba(30, 144, 255, 0.02), rgba(30, 144, 255, 0.05)); background-size: 100% 4px, 6px 100%;"></div>

                            <!-- Camera Feed Mock Visual -->
                            <div class="text-center text-light opacity-50">
                                <i class="bi bi-camera-video" style="font-size: 2.5rem; color: var(--color-text-inverse);"></i>
                                <div class="mt-2" style="font-size: 10px; font-family: monospace; letter-spacing: 1px;">RTSP FEED ACTIVE</div>
                            </div>

                            <!-- Live overlay badge -->
                            <div class="position-absolute top-0 start-0 m-3 d-flex gap-2 align-items-center">
                                @if($camera->status->value === 'online')
                                    <span class="badge bg-success bg-opacity-75 text-white border-0 py-1 px-2 d-flex align-items-center gap-1" style="font-size: 10px; border-radius: 4px;">
                                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true" style="width: 8px; height: 8px;"></span> LIVE
                                    </span>
                                @elseif($camera->status->value === 'offline')
                                    <span class="badge bg-danger bg-opacity-75 text-white border-0 py-1 px-2" style="font-size: 10px; border-radius: 4px;">
                                        OFFLINE
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark border-0 py-1 px-2" style="font-size: 10px; border-radius: 4px;">
                                        MAINTENANCE
                                    </span>
                                @endif

                                @if($camera->access->value === 'public')
                                    <span class="badge bg-info bg-opacity-75 text-white border-0 py-1 px-2" style="font-size: 10px; border-radius: 4px;">
                                        <i class="bi bi-share-fill" style="font-size: 8px;"></i> PUBLIK
                                    </span>
                                @endif
                            </div>

                            <!-- Top right Category -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge" style="background-color: {{ $camera->category->bg_color ?? '#e2e5ec' }}; color: {{ $camera->category->text_color ?? '#232d42' }}; border-radius: 4px; font-size: 10px; padding: 4px 8px;">
                                    <i class="{{ $camera->category->icon ?? 'bi bi-tag-fill' }} me-1"></i>{{ $camera->category->name }}
                                </span>
                            </div>

                            <!-- Overlay IP details -->
                            <div class="position-absolute bottom-0 start-0 m-3 text-white" style="font-size: 11px; font-family: monospace; text-shadow: 1px 1px 2px rgba(0,0,0,0.8);">
                                IP: {{ $camera->ip_address }}
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-custom-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-0 fw-bold text-truncate" style="color: var(--color-text-tertiary); max-width: 180px;">{{ $camera->name }}</h5>
                                    <small class="text-muted" style="font-size: 12px;">{{ $camera->brand }} / {{ $camera->model }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="text-secondary d-block" style="font-size: 11px;"><i class="bi bi-geo-alt-fill text-primary"></i> {{ $camera->location ? $camera->location->name : 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <hr class="my-2 opacity-50">

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" wire:click="openShare({{ $camera->id }})" class="btn btn-sm btn-outline-info d-flex align-items-center gap-1" style="font-size: var(--font-size-xs); padding: 5px 10px; border-radius: var(--radius-xs);">
                                    <i class="bi bi-share"></i> Bagikan
                                </button>
                                <div class="d-flex gap-2">
                                    @can('camera.update')
                                        <button type="button" wire:click="edit({{ $camera->id }})" class="btn btn-sm btn-outline-primary" style="border-radius: var(--radius-xs);" title="Ubah Kamera">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endcan
                                    @can('camera.delete')
                                        <button type="button" wire:click="delete({{ $camera->id }})" wire:confirm="Apakah Anda yakin ingin menghapus kamera ini?" style="border-radius: var(--radius-xs);" class="btn btn-sm btn-outline-danger" title="Hapus Kamera">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted card-custom border-0 bg-white">
                    <i class="bi bi-camera-video-off d-block mb-3" style="font-size: 3rem;"></i>
                    Tidak ada data kamera CCTV ditemukan.
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="card-custom border-0 bg-light p-3 mb-4">
            {{ $cameras->links() }}
        </div>
    @else
        <!-- Table View -->
        <div class="card-custom border-0 overflow-hidden">
            <div class="card-custom-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Nama Kamera</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">IP Address</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Status</th>
                                <th scope="col">Akses Publik</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cameras as $camera)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $camera->name }}</div>
                                        <small class="text-muted">{{ $camera->brand }} / {{ $camera->model }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $camera->category->bg_color ?? '#e2e5ec' }}; color: {{ $camera->category->text_color ?? '#232d42' }}; padding: var(--space-2) var(--space-3); border-radius: var(--radius-xs);">
                                            <i class="{{ $camera->category->icon ?? 'bi bi-camera' }} me-1"></i>{{ $camera->category->name }}
                                        </span>
                                    </td>
                                    <td><code class="text-dark">{{ $camera->ip_address }}</code></td>
                                    <td>{{ $camera->location ? $camera->location->name : 'N/A' }}</td>
                                    <td>
                                        @if($camera->status->value === 'online')
                                            <span class="badge-custom badge-success">Online</span>
                                        @elseif($camera->status->value === 'offline')
                                            <span class="badge-custom badge-danger">Offline</span>
                                        @else
                                            <span class="badge-custom badge-warning">Maintenance</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($camera->access->value === 'public')
                                            <span class="badge-custom badge-info" title="Publik">
                                                <i class="bi bi-share-fill"></i> Publik
                                            </span>
                                        @else
                                            <span class="badge-custom badge-secondary">
                                                <i class="bi bi-lock-fill"></i> Privat
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <button type="button" wire:click="openShare({{ $camera->id }})" class="btn btn-sm btn-outline-info" title="Manage Sharing Link">
                                                <i class="bi bi-share"></i>
                                            </button>
                                            @can('camera.update')
                                                <button type="button" wire:click="edit({{ $camera->id }})" class="btn btn-sm btn-outline-primary" title="Edit Camera">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            @endcan
                                            @can('camera.delete')
                                                <button type="button" wire:click="delete({{ $camera->id }})" wire:confirm="Apakah Anda yakin ingin menghapus kamera ini?" class="btn btn-sm btn-outline-danger" title="Delete Camera">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-camera-video-off d-block mb-3" style="font-size: 3rem;"></i>
                                        Tidak ada data kamera CCTV ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-custom-body bg-light border-top p-3">
                {{ $cameras->links() }}
            </div>
        </div>
    @endif

    <!-- Camera Form Modal -->
    @if($isFormOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">
                            {{ $cameraId ? 'Ubah Kamera CCTV' : 'Tambah Kamera CCTV Baru' }}
                        </h5>
                        <button type="button" wire:click="closeForm" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Nama Kamera *</label>
                                    <input type="text" wire:model="name" class="form-control form-control-custom @error('name') is-invalid @enderror" placeholder="CCTV Lobby Utama">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Brand *</label>
                                    <input type="text" wire:model="brand" class="form-control form-control-custom @error('brand') is-invalid @enderror" placeholder="Hikvision">
                                    @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Model *</label>
                                    <input type="text" wire:model="model" class="form-control form-control-custom @error('model') is-invalid @enderror" placeholder="DS-2CD2021G1-I">
                                    @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">IP Address *</label>
                                    <input type="text" wire:model="ip_address" class="form-control form-control-custom @error('ip_address') is-invalid @enderror" placeholder="192.168.1.10">
                                    @error('ip_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">RTSP URL * (Disimpan terenkripsi)</label>
                                    <input type="text" wire:model="rtsp_url" class="form-control form-control-custom @error('rtsp_url') is-invalid @enderror" placeholder="rtsp://admin:password@192.168.1.10:554/h264">
                                    @error('rtsp_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Lokasi *</label>
                                    <select wire:model="location_id" class="form-select form-control-custom @error('location_id') is-invalid @enderror">
                                        <option value="">Pilih Lokasi</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Kategori *</label>
                                    <select wire:model="category_id" class="form-select form-control-custom @error('category_id') is-invalid @enderror">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Status *</label>
                                    <select wire:model="status" class="form-select form-control-custom @error('status') is-invalid @enderror">
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Latitude (Untuk Peta)</label>
                                    <input type="text" wire:model="latitude" class="form-control form-control-custom @error('latitude') is-invalid @enderror" placeholder="-6.2088">
                                    @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Longitude (Untuk Peta)</label>
                                    <input type="text" wire:model="longitude" class="form-control form-control-custom @error('longitude') is-invalid @enderror" placeholder="106.8456">
                                    @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" wire:click="closeForm" class="btn btn-secondary-custom">Batal</button>
                            <button type="submit" class="btn btn-primary-custom">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Share Link Modal -->
    @if($isShareOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content border-0" style="border-radius: var(--radius-md);">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" style="color: var(--color-text-tertiary);">Akses Tautan Berbagi</h5>
                        <button type="button" wire:click="closeShare" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <strong>Nama Kamera:</strong> {{ $sharingCamera->name }}
                        </div>
                        <div class="mb-3">
                            <strong>Status Publik Saat Ini:</strong>
                            @if($sharingCamera->access->value === 'public')
                                <span class="badge-custom badge-info">Aktif (Publik)</span>
                            @else
                                <span class="badge-custom badge-secondary">Non-Aktif (Privat)</span>
                            @endif
                        </div>

                        @if($sharingCamera->access->value === 'public')
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Tautan Berbagi:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly value="{{ route('cameras.public', $sharingCamera->public_token) }}" id="publicLinkInput">
                                    <button type="button" class="btn btn-primary-custom" onclick="navigator.clipboard.writeText(document.getElementById('publicLinkInput').value); alert('Tautan disalin ke clipboard!');">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                </div>
                                <small class="text-success d-block mt-1">Berlaku permanen.</small>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mt-4">
                            @if($sharingCamera->access->value === 'public')
                                <button type="button" wire:click="revokeShareLink" class="btn btn-danger">Cabut Akses</button>
                            @else
                                <div></div>
                            @endif
                            <div class="d-flex gap-2">
                                <button type="button" wire:click="closeShare" class="btn btn-secondary-custom">Tutup</button>
                                <button type="button" wire:click="generateShareLink" class="btn btn-primary-custom">Buat / Perbarui Tautan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
