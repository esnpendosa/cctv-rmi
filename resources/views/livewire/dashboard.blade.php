<div>
    <!-- Metrics Row -->
    <div class="row">
        <!-- Total CCTV Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card-custom">
                <div class="card-custom-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: var(--font-size-xs);">Total Kamera</span>
                        <h3 class="mb-0 mt-1" style="color: var(--color-text-tertiary);">{{ $cameraStats['total'] }}</h3>
                    </div>
                    <div class="rounded bg-primary-subtle p-3 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-camera-video fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Online CCTV Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card-custom">
                <div class="card-custom-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: var(--font-size-xs);">Kamera Online</span>
                        <h3 class="mb-0 mt-1 text-success">{{ $cameraStats['online'] }}</h3>
                    </div>
                    <div class="rounded bg-success-subtle p-3 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-cloud-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Offline CCTV Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card-custom">
                <div class="card-custom-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: var(--font-size-xs);">Kamera Offline</span>
                        <h3 class="mb-0 mt-1 text-danger">{{ $cameraStats['offline'] }}</h3>
                    </div>
                    <div class="rounded bg-danger-subtle p-3 text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-cloud-slash fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Low Stock Alerts Card -->
        <div class="col-md-3 col-sm-6">
            <div class="card-custom">
                <div class="card-custom-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: var(--font-size-xs);">Menipis (Stok)</span>
                        <h3 class="mb-0 mt-1 {{ $lowStockCount > 0 ? 'text-warning' : '' }}">{{ $lowStockCount }} Item</h3>
                    </div>
                    <div class="rounded bg-warning-subtle p-3 text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Live Stream & Interactive Map -->
        <div class="col-lg-8">
            <!-- Leaflet Map Card -->
            <div class="card-custom">
                <div class="card-custom-header">
                    <span class="fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Peta Lokasi CCTV</span>
                    <span class="text-muted" style="font-size: var(--font-size-xs);">Distribusi Geografis Kamera</span>
                </div>
                <div class="card-custom-body p-0">
                    <div id="cctvMap" style="height: 350px; z-index: 10;"></div>
                </div>
            </div>

            <!-- CCTV Live Grid Card -->
            <div class="card-custom">
                <div class="card-custom-header">
                    <span class="fw-bold"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Multi-Stream CCTV Grid</span>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('monitor.wall') }}" target="_blank" class="btn btn-sm btn-secondary-custom d-flex align-items-center gap-1" style="padding: 4px 10px; font-size: var(--font-size-xs); border-radius: var(--radius-xs);">
                            <i class="bi bi-fullscreen"></i> Layar Penuh
                        </a>
                        <span class="badge-custom badge-brand">Live Feed</span>
                    </div>
                </div>
                <div class="card-custom-body">
                    @if($gridCameras->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-camera-video-off d-block mb-3" style="font-size: 3rem;"></i>
                            Tidak ada kamera yang online untuk ditampilkan.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($gridCameras as $camera)
                                <div class="col-md-6">
                                    <div class="position-relative bg-black rounded overflow-hidden" style="aspect-ratio: 16/9;">
                                        <!-- Mock Video Overlay -->
                                        <div class="position-absolute top-0 start-0 w-100 p-2 d-flex justify-content-between text-white z-2" style="font-family: monospace; font-size: 10px; background: linear-gradient(to bottom, rgba(0,0,0,0.6), transparent);">
                                            <span>{{ $camera->name }}</span>
                                            <span class="text-success">● LIVE</span>
                                        </div>
                                        <video class="w-100 h-100 object-fit-cover opacity-75" autoplay loop muted playsinline>
                                            <source src="https://assets.mixkit.co/videos/preview/mixkit-cctv-camera-of-a-street-with-people-41712-large.mp4" type="video/mp4">
                                        </video>
                                        <div class="position-absolute bottom-0 start-0 w-100 p-2 text-white z-2 d-flex justify-content-between" style="font-family: monospace; font-size: 10px; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
                                            <span>{{ $camera->location ? $camera->location->name : 'N/A' }}</span>
                                            <span>IP: {{ $camera->ip_address }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Monitoring Logs Feed -->
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-custom-header">
                    <span class="fw-bold"><i class="bi bi-clock-history text-info me-2"></i>Aktivitas Pemantauan</span>
                    <span class="badge bg-secondary" style="font-size: 0.75rem;">10 Terkini</span>
                </div>
                <div class="card-custom-body p-0" style="max-height: 700px; overflow-y: auto;">
                    @if($latestLogs->isEmpty())
                        <div class="text-center py-5 text-muted">
                            Tidak ada data log pemantauan.
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($latestLogs as $log)
                                <div class="list-group-item p-3 border-0 border-bottom">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 text-dark fw-semibold" style="font-size: var(--font-size-sm);">
                                            {{ $log->camera ? $log->camera->name : 'Kamera Terhapus' }}
                                        </h6>
                                        <small class="text-muted">{{ $log->recorded_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-secondary" style="font-size: var(--font-size-xs);">
                                        Event: <span class="badge-custom {{ $log->event === 'online' ? 'badge-success' : 'badge-danger' }}">{{ $log->event }}</span>
                                    </p>
                                    @if($log->details)
                                        <small class="text-muted d-block" style="font-size: 11px; font-family: monospace;">
                                            {{ $log->details }}
                                        </small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet Init Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('cctvMap').setView([-6.2088, 106.8456], 11); // Center in Jakarta

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const locations = @json($locations);
            
            locations.forEach(function(loc) {
                let color = 'green';
                if (loc.offline_count > 0 && loc.online_count > 0) {
                    color = 'orange';
                } else if (loc.offline_count > 0 && loc.online_count === 0) {
                    color = 'red';
                }

                // Simple styled SVG marker representing status
                const icon = L.divIcon({
                    html: `<div style="background-color: ${color}; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>`,
                    className: 'custom-div-icon',
                    iconSize: [15, 15],
                    iconAnchor: [7, 7]
                });

                const popupContent = `
                    <div style="font-family: var(--font-primary);">
                        <h6 style="margin: 0 0 5px 0; color: var(--color-text-tertiary); font-weight: 700;">${loc.name}</h6>
                        <p style="margin: 0 0 10px 0; font-size: 11px; color: var(--color-text-secondary);">${loc.address}</p>
                        <div style="font-size: 11px;">
                            <strong>Kamera:</strong> ${loc.cameras_count}<br>
                            <span style="color: green;">✔ Online: ${loc.online_count}</span><br>
                            <span style="color: red;">❌ Offline: ${loc.offline_count}</span>
                        </div>
                    </div>
                `;

                L.marker([loc.latitude, loc.longitude], {icon: icon})
                    .addTo(map)
                    .bindPopup(popupContent);
            });
        });
    </script>
</div>
