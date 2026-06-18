<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitor CCTV — {{ config('app.name', 'CCTV RMI') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@300;400;500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ─── Monitor Wall Dark Theme ─────────────────────────── */
        :root {
            --mon-bg:        #0a0c10;
            --mon-surface:   #111520;
            --mon-card:      #161b27;
            --mon-border:    rgba(255,255,255,0.07);
            --mon-accent:    #4361ee;
            --mon-online:    #22c55e;
            --mon-offline:   #ef4444;
            --mon-maint:     #f59e0b;
            --mon-text:      #e2e8f0;
            --mon-muted:     #64748b;
            --mon-font:      'Lexend Deca', system-ui, sans-serif;
            --mon-mono:      'Share Tech Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            margin: 0; padding: 0;
            background: var(--mon-bg);
            color: var(--mon-text);
            font-family: var(--mon-font);
            height: 100%;
            overflow-x: hidden;
        }

        /* ── TOP BAR ── */
        .mon-topbar {
            height: 56px;
            background: var(--mon-surface);
            border-bottom: 1px solid var(--mon-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .mon-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .mon-logo-icon {
            width: 36px; height: 36px;
            background: var(--mon-accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff;
        }
        .mon-logo-text {
            font-size: 15px;
            font-weight: 700;
            color: var(--mon-text);
            letter-spacing: 0.3px;
        }
        .mon-logo-sub {
            font-size: 10px;
            color: var(--mon-muted);
            font-weight: 400;
        }
        .mon-clock {
            font-family: var(--mon-mono);
            font-size: 13px;
            color: var(--mon-online);
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mon-clock-dot {
            width: 7px; height: 7px;
            background: var(--mon-online);
            border-radius: 50%;
            animation: blink 1.2s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* ── STATS BAR ── */
        .mon-statsbar {
            background: var(--mon-surface);
            border-bottom: 1px solid var(--mon-border);
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .mon-stat {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .mon-stat-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
        }
        .mon-stat.online  .mon-stat-dot { background: var(--mon-online); box-shadow: 0 0 6px var(--mon-online); }
        .mon-stat.offline .mon-stat-dot { background: var(--mon-offline); }
        .mon-stat.maint   .mon-stat-dot { background: var(--mon-maint); }
        .mon-stat.total   .mon-stat-dot { background: var(--mon-accent); }

        /* ── CONTROLS BAR ── */
        .mon-controls {
            background: var(--mon-surface);
            border-bottom: 1px solid var(--mon-border);
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .mon-filter-btn {
            padding: 4px 14px;
            border-radius: 6px;
            border: 1px solid var(--mon-border);
            background: transparent;
            color: var(--mon-muted);
            font-size: 12px;
            font-family: var(--mon-font);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }
        .mon-filter-btn:hover,
        .mon-filter-btn.active {
            background: var(--mon-accent);
            border-color: var(--mon-accent);
            color: #fff;
        }
        .mon-grid-btn {
            width: 30px; height: 30px;
            border-radius: 6px;
            border: 1px solid var(--mon-border);
            background: transparent;
            color: var(--mon-muted);
            font-size: 13px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }
        .mon-grid-btn:hover,
        .mon-grid-btn.active {
            background: var(--mon-accent);
            border-color: var(--mon-accent);
            color: #fff;
        }
        .mon-sep { width: 1px; height: 24px; background: var(--mon-border); flex-shrink: 0; }

        /* ── GRID ── */
        .mon-grid {
            display: grid;
            gap: 6px;
            padding: 12px 16px;
        }
        .mon-grid.cols-1 { grid-template-columns: 1fr; }
        .mon-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
        .mon-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
        .mon-grid.cols-4 { grid-template-columns: repeat(4, 1fr); }
        @media (max-width: 900px)  { .mon-grid.cols-4,.mon-grid.cols-3 { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 540px)  { .mon-grid { grid-template-columns: 1fr !important; } }

        /* ── CAMERA CARD ── */
        .cam-card {
            position: relative;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--mon-border);
            aspect-ratio: 16/9;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .cam-card:hover {
            border-color: var(--mon-accent);
            box-shadow: 0 0 0 2px rgba(67,97,238,0.35);
        }
        .cam-card.is-online  { border-top: 2px solid var(--mon-online); }
        .cam-card.is-offline { border-top: 2px solid var(--mon-offline); }
        .cam-card.is-maint   { border-top: 2px solid var(--mon-maint); }

        /* Scanline CRT overlay */
        .cam-scanlines {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(0,0,0,0) 50%, rgba(0,0,0,0.18) 50%),
                linear-gradient(90deg, rgba(255,0,0,0.03), rgba(0,255,0,0.02), rgba(0,0,255,0.03));
            background-size: 100% 3px, 4px 100%;
            pointer-events: none;
            z-index: 3;
        }

        /* Video / iframe */
        .cam-video {
            position: absolute;
            inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: 1;
        }
        .cam-video iframe {
            width: 100%; height: 100%;
            border: none;
        }

        /* Offline/Maintenance overlay */
        .cam-overlay-msg {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2;
            gap: 8px;
        }
        .cam-overlay-msg i { font-size: 2rem; opacity: 0.5; }
        .cam-overlay-msg span { font-size: 11px; font-weight: 600; opacity: 0.5; letter-spacing: 1px; text-transform: uppercase; }

        /* Top overlay: camera info */
        .cam-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            padding: 6px 10px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.80) 0%, transparent 100%);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            z-index: 4;
            font-family: var(--mon-mono);
            font-size: 10px;
        }
        .cam-top-name {
            font-size: 11px;
            font-weight: 700;
            font-family: var(--mon-font);
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 70%;
        }
        .cam-live-badge {
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(34,197,94,0.85);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 2px 7px;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .cam-live-dot {
            width: 6px; height: 6px;
            background: #fff;
            border-radius: 50%;
            animation: blink 1s infinite;
        }
        .cam-offline-badge {
            background: rgba(239,68,68,0.85);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 2px 7px;
            border-radius: 4px;
            flex-shrink: 0;
        }
        .cam-maint-badge {
            background: rgba(245,158,11,0.85);
            color: #000;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 2px 7px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        /* Bottom overlay: location + IP */
        .cam-bottom {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 6px 10px;
            background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            z-index: 4;
            font-family: var(--mon-mono);
            font-size: 9px;
            color: rgba(255,255,255,0.7);
        }

        /* ── FULLSCREEN MODAL ── */
        .mon-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.97);
            z-index: 9999;
            display: flex;
            flex-direction: column;
        }
        .mon-modal-header {
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: var(--mon-surface);
            border-bottom: 1px solid var(--mon-border);
            flex-shrink: 0;
        }
        .mon-modal-body {
            flex: 1;
            position: relative;
            overflow: hidden;
        }
        .mon-modal-body video,
        .mon-modal-body iframe {
            width: 100%; height: 100%;
            object-fit: contain;
            border: none;
        }
        .mon-close-btn {
            background: var(--mon-offline);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 4px 14px;
            font-size: 13px;
            cursor: pointer;
            font-family: var(--mon-font);
        }

        /* ── NO CAMERAS ── */
        .mon-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 80px 20px;
            color: var(--mon-muted);
        }
        .mon-empty i { font-size: 4rem; display: block; margin-bottom: 12px; opacity: 0.3; }
        .mon-empty p { font-size: 14px; }

        /* ── FOOTER ── */
        .mon-footer {
            text-align: center;
            padding: 10px;
            font-size: 10px;
            color: var(--mon-muted);
            border-top: 1px solid var(--mon-border);
            letter-spacing: 0.5px;
        }
    </style>

    @livewireStyles
</head>
<body>
    {{ $slot }}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts

    <script>
        /* ── Live clock update ── */
        function updateMonClock() {
            const el = document.getElementById('monClock');
            if (!el) return;
            const now = new Date();
            const pad = n => String(n).padStart(2,'0');
            el.textContent =
                `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())} `+
                `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        }
        setInterval(updateMonClock, 1000);
        updateMonClock();

        /* ── Fullscreen modal ── */
        function openCamModal(name, location, ip, status, streamUrl) {
            document.getElementById('modal-cam-name').textContent = name;
            document.getElementById('modal-cam-loc').textContent  = location;

            const body = document.getElementById('modal-body');
            body.innerHTML = '';

            if (status === 'online') {
                if (streamUrl && streamUrl.startsWith('http')) {
                    // go2rtc WebRTC iframe
                    const iframe = document.createElement('iframe');
                    iframe.src = streamUrl;
                    iframe.allowFullscreen = true;
                    iframe.allow = 'camera; microphone; autoplay';
                    body.appendChild(iframe);
                } else {
                    const v = document.createElement('video');
                    v.autoplay = true; v.muted = true; v.loop = true; v.controls = true;
                    v.style.cssText = 'width:100%;height:100%;object-fit:contain;background:#000';
                    const src = document.createElement('source');
                    src.src  = 'https://assets.mixkit.co/videos/preview/mixkit-cctv-camera-of-a-street-with-people-41712-large.mp4';
                    src.type = 'video/mp4';
                    v.appendChild(src);
                    body.appendChild(v);
                }
            } else {
                body.innerHTML = `<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:#555;gap:12px;">
                    <i class="bi bi-camera-video-off" style="font-size:4rem;"></i>
                    <span style="font-size:1rem;letter-spacing:2px;text-transform:uppercase;">${status === 'offline' ? 'Kamera Offline' : 'Maintenance'}</span>
                </div>`;
            }

            document.getElementById('camModal').style.display = 'flex';
        }

        function closeCamModal() {
            const body = document.getElementById('modal-body');
            body.innerHTML = '';
            document.getElementById('camModal').style.display = 'none';
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeCamModal();
        });
    </script>
</body>
</html>
