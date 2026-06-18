<div>
    @if($error)
        <div class="alert alert-danger border-0 shadow-sm text-center py-4 mb-0" style="border-radius: var(--radius-sm);">
            <i class="bi bi-shield-slash-fill d-block mb-3" style="font-size: 2.5rem;"></i>
            <h5 class="fw-bold">Akses Ditolak</h5>
            <p class="mb-0 text-secondary" style="font-size: var(--font-size-sm);">{{ $error }}</p>
        </div>
    @else
        <div class="card-custom border-0 shadow-sm mb-0 overflow-hidden" style="border-radius: var(--radius-md);">
            <div class="card-custom-header" style="background-color: var(--color-surface-raised);">
                <span class="fw-bold" style="color: var(--color-text-tertiary);">
                    <i class="bi bi-camera-video-fill text-primary me-2"></i>{{ $camera->name }}
                </span>
                <span class="badge-custom badge-success d-inline-flex align-items-center gap-1">
                    <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span> Live
                </span>
            </div>
            
            <!-- Video Container -->
            <div class="position-relative bg-dark" style="aspect-ratio: 16/9; overflow: hidden;">
                <!-- Scanner overlay and info -->
                <div class="position-absolute top-0 start-0 w-100 p-3 d-flex justify-content-between text-white z-2" style="font-family: monospace; font-size: var(--font-size-xs); background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent); text-shadow: 1px 1px 2px black;">
                    <div>
                        <div>CAM: {{ strtoupper($camera->brand) }} / {{ strtoupper($camera->model) }}</div>
                        <div>IP: {{ $camera->ip_address }}</div>
                        <div>LOC: {{ $camera->location ? strtoupper($camera->location->name) : 'N/A' }}</div>
                    </div>
                    <div class="text-end" id="liveTime">
                        {{ now()->format('Y-m-d') }}<br>
                        <span id="clock">00:00:00</span>
                    </div>
                </div>

                <!-- CRT scanline animation and video simulator -->
                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-black position-relative">
                    <div class="position-absolute w-100 h-100 pointer-events-none" style="background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06)); background-size: 100% 4px, 6px 100%; z-index: 1;"></div>
                    
                    <!-- Simulating Active CCTV Stream -->
                    <video class="w-100 h-100 object-fit-cover" autoplay loop muted playsinline poster="https://picsum.photos/id/111/800/450">
                        <source src="https://assets.mixkit.co/videos/preview/mixkit-cctv-camera-of-a-street-with-people-41712-large.mp4" type="video/mp4">
                        Video stream not supported.
                    </video>
                </div>

                <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white z-2" style="font-family: monospace; font-size: var(--font-size-xs); background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); text-shadow: 1px 1px 2px black;">
                    REC <span class="text-danger">●</span> CLOUD_STOR_OK
                </div>
            </div>
            
            <div class="card-custom-body bg-light text-center p-3">
                <p class="mb-0 text-muted" style="font-size: var(--font-size-xs);">
                    Tautan berbagi aman. Dikelola oleh Sistem CCTV RMI.
                </p>
            </div>
        </div>

        <script>
            function updateClock() {
                const now = new Date();
                const timeString = now.toTimeString().split(' ')[0];
                const clockEl = document.getElementById('clock');
                if (clockEl) clockEl.textContent = timeString;
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>
    @endif
</div>
