<div>
    {{-- ══════════════════════════════════════════════════════════
         TOP BAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="mon-topbar">
        <a href="{{ route('monitor.wall') }}" class="mon-logo">
            <div class="mon-logo-icon">
                <i class="bi bi-camera-video-fill"></i>
            </div>
            <div>
                <div class="mon-logo-text">CCTV RMI</div>
                <div class="mon-logo-sub">MONITORING WALL · REAL-TIME</div>
            </div>
        </a>

        <div class="mon-clock">
            <span class="mon-clock-dot"></span>
            <span id="monClock">--:--:--</span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         STATS BAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="mon-statsbar">
        <div class="mon-stat total">
            <span class="mon-stat-dot"></span>
            <span>{{ $stats['total'] }} Total Kamera</span>
        </div>
        <div class="mon-sep"></div>
        <div class="mon-stat online">
            <span class="mon-stat-dot"></span>
            <span>{{ $stats['online'] }} Online</span>
        </div>
        <div class="mon-stat offline">
            <span class="mon-stat-dot"></span>
            <span>{{ $stats['offline'] }} Offline</span>
        </div>
        <div class="mon-stat maint">
            <span class="mon-stat-dot"></span>
            <span>{{ $stats['maintenance'] }} Maintenance</span>
        </div>
        <div class="ms-auto" style="font-size:11px;color:#64748b;">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Auto-refresh aktif
            <span wire:poll.30s></span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         CONTROLS BAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="mon-controls">
        {{-- Location filter --}}
        <button type="button"
                wire:click="$set('activeLocation', '')"
                class="mon-filter-btn {{ $activeLocation === '' ? 'active' : '' }}">
            <i class="bi bi-globe2 me-1"></i>Semua Area
        </button>

        @foreach($locations as $loc)
            <button type="button"
                    wire:click="$set('activeLocation', '{{ $loc->id }}')"
                    class="mon-filter-btn {{ $activeLocation == $loc->id ? 'active' : '' }}">
                <i class="bi bi-geo-alt me-1"></i>{{ $loc->name }}
            </button>
        @endforeach

        <div class="mon-sep"></div>

        {{-- Grid column picker --}}
        <button type="button"
                wire:click="$set('gridCols', '1')"
                class="mon-grid-btn {{ $gridCols === '1' ? 'active' : '' }}"
                title="1 Kolom">
            <i class="bi bi-square"></i>
        </button>
        <button type="button"
                wire:click="$set('gridCols', '2')"
                class="mon-grid-btn {{ $gridCols === '2' ? 'active' : '' }}"
                title="2 Kolom">
            <i class="bi bi-grid"></i>
        </button>
        <button type="button"
                wire:click="$set('gridCols', '3')"
                class="mon-grid-btn {{ $gridCols === '3' ? 'active' : '' }}"
                title="3 Kolom">
            <i class="bi bi-grid-3x2-gap"></i>
        </button>
        <button type="button"
                wire:click="$set('gridCols', '4')"
                class="mon-grid-btn {{ $gridCols === '4' ? 'active' : '' }}"
                title="4 Kolom">
            <i class="bi bi-grid-3x3-gap"></i>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         CAMERA GRID
    ══════════════════════════════════════════════════════════ --}}
    <div class="mon-grid cols-{{ $gridCols }}">
        @forelse($cameras as $camera)
            @php
                $statusVal  = $camera->status->value ?? 'offline';
                $isOnline   = $statusVal === 'online';
                $isOffline  = $statusVal === 'offline';
                $isMaint    = $statusVal === 'maintenance';
                $locName    = $camera->location?->name ?? 'N/A';
                $streamUrl  = $isOnline
                    ? 'http://' . $go2rtcHost . ':' . $go2rtcPort . '/stream.html?src=' . urlencode($camera->name)
                    : '';
            @endphp

            <div class="cam-card {{ $isOnline ? 'is-online' : ($isMaint ? 'is-maint' : 'is-offline') }}"
                 onclick="openCamModal(
                     '{{ addslashes($camera->name) }}',
                     '{{ addslashes($locName) }}',
                     '',
                     '{{ $statusVal }}',
                     '{{ addslashes($streamUrl) }}'
                 )"
                 title="{{ $camera->name }} — Klik untuk perbesar">

                {{-- Scanlines --}}
                <div class="cam-scanlines"></div>

                {{-- Video / Placeholder --}}
                <div class="cam-video">
                    @if($isOnline)
                        {{-- Try go2rtc WebRTC iframe embed --}}
                        <iframe
                            src="{{ $streamUrl }}&controls=0&autoplay=1&muted=1"
                            allow="autoplay; camera; microphone"
                            loading="lazy"
                            style="width:100%;height:100%;border:none;display:block;"
                            onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        </iframe>
                        {{-- Fallback mock --}}
                        <div style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;background:#050810;">
                            <video autoplay loop muted playsinline style="width:100%;height:100%;object-fit:cover;">
                                <source src="https://assets.mixkit.co/videos/preview/mixkit-cctv-camera-of-a-street-with-people-41712-large.mp4" type="video/mp4">
                            </video>
                        </div>
                    @elseif($isMaint)
                        <div class="cam-overlay-msg" style="background:#0c0a00;">
                            <i class="bi bi-tools" style="color:#f59e0b;"></i>
                            <span style="color:#f59e0b;">Maintenance</span>
                        </div>
                    @else
                        <div class="cam-overlay-msg" style="background:#0a0000;">
                            <i class="bi bi-camera-video-off" style="color:#ef4444;"></i>
                            <span style="color:#ef4444;">Offline</span>
                        </div>
                    @endif
                </div>

                {{-- Top overlay --}}
                <div class="cam-top">
                    <div>
                        <div class="cam-top-name">{{ $camera->name }}</div>
                        <div style="font-size:9px;color:rgba(255,255,255,0.55);margin-top:1px;">
                            <i class="bi bi-geo-alt-fill"></i> {{ $locName }}
                        </div>
                    </div>
                    @if($isOnline)
                        <div class="cam-live-badge">
                            <span class="cam-live-dot"></span>LIVE
                        </div>
                    @elseif($isMaint)
                        <div class="cam-maint-badge">MAINT</div>
                    @else
                        <div class="cam-offline-badge">OFFLINE</div>
                    @endif
                </div>

                {{-- Bottom overlay --}}
                <div class="cam-bottom">
                    <div>
                        <i class="bi bi-cpu me-1"></i>{{ strtoupper($camera->brand) }} {{ strtoupper($camera->model) }}
                    </div>
                    <div style="text-align:right;">
                        @if($camera->category)
                            <span style="color:rgba(255,255,255,0.55);">[ {{ $camera->category->name }} ]</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="mon-empty">
                <i class="bi bi-camera-video-off"></i>
                <p>Tidak ada kamera yang ditemukan untuk area ini.</p>
                @auth
                    <a href="{{ route('cameras.index') }}" class="mon-filter-btn" style="display:inline-block;text-decoration:none;margin-top:8px;">
                        + Tambah Kamera
                    </a>
                @endauth
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════════════════════ --}}
    <div class="mon-footer">
        CCTV RMI · Sistem Pemantauan CCTV Real-Time ·
        <span style="color:#4361ee;">{{ $stats['online'] }}/{{ $stats['total'] }}</span> kamera aktif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FULLSCREEN MODAL
    ══════════════════════════════════════════════════════════ --}}
    <div id="camModal"
         class="mon-modal-backdrop"
         style="display:none;"
         onclick="if(event.target===this)closeCamModal()">

        <div class="mon-modal-header">
            <div class="d-flex align-items-center gap-3">
                <div style="width:8px;height:8px;border-radius:50%;background:#22c55e;animation:blink 1s infinite;"></div>
                <div>
                    <div id="modal-cam-name" style="font-size:15px;font-weight:700;color:#e2e8f0;"></div>
                    <div id="modal-cam-loc" style="font-size:11px;color:#64748b;font-family:'Share Tech Mono',monospace;"></div>
                </div>
            </div>
            <button class="mon-close-btn" onclick="closeCamModal()">
                <i class="bi bi-x-lg me-1"></i>Tutup
            </button>
        </div>

        <div class="mon-modal-body" id="modal-body">
            {{-- Filled via JS --}}
        </div>
    </div>
</div>
