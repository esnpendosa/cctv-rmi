<div>
    <!-- Title Page -->
    <div class="mb-4">
        <h2 class="mb-0" style="color: var(--color-text-tertiary); font-weight: 700;">Laporan & Analisis</h2>
        <p class="text-muted mb-0" style="font-size: var(--font-size-sm);">Analisis performa sistem monitoring CCTV, rekap keuangan, evaluasi inventaris, dan kinerja klien.</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="card-custom border-0 mb-4 overflow-hidden">
        <div class="bg-light p-2 border-bottom">
            <ul class="nav nav-pills gap-2" style="border: none;">
                <li class="nav-item">
                    <button type="button" wire:click="changeTab('monitoring')" class="btn {{ $activeTab === 'monitoring' ? 'btn-primary-custom' : 'btn-light text-dark' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold">
                        <i class="bi bi-camera-video"></i> Monitoring CCTV
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" wire:click="changeTab('revenue')" class="btn {{ $activeTab === 'revenue' ? 'btn-primary-custom' : 'btn-light text-dark' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold">
                        <i class="bi bi-cash-coin"></i> Keuangan (Revenue)
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" wire:click="changeTab('inventory')" class="btn {{ $activeTab === 'inventory' ? 'btn-primary-custom' : 'btn-light text-dark' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold">
                        <i class="bi bi-box-seam"></i> Inventaris & Stok
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" wire:click="changeTab('clients')" class="btn {{ $activeTab === 'clients' ? 'btn-primary-custom' : 'btn-light text-dark' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold">
                        <i class="bi bi-people"></i> Kinerja Klien
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab 1: Monitoring CCTV -->
    @if($activeTab === 'monitoring')
        <!-- Filters -->
        <div class="card-custom border-0 mb-4">
            <div class="card-custom-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Dari Tanggal</label>
                        <input type="date" wire:model.live="m_start_date" class="form-control form-control-custom">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Sampai Tanggal</label>
                        <input type="date" wire:model.live="m_end_date" class="form-control form-control-custom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Kamera</label>
                        <select wire:model.live="m_camera_id" class="form-select form-control-custom">
                            <option value="">Semua Kamera</option>
                            @foreach($cameras as $cam)
                                <option value="{{ $cam->id }}">{{ $cam->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Lokasi</label>
                        <select wire:model.live="m_location_id" class="form-select form-control-custom">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark">Kategori</label>
                        <select wire:model.live="m_category_id" class="form-select form-control-custom">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Uptime Stats Grid -->
        <h5 class="fw-bold mb-3 text-dark">Uptime Kamera CCTV</h5>
        <div class="row g-3 mb-4">
            @forelse($uptimeStats as $stat)
                <div class="col-md-3">
                    <div class="card-custom border-0 p-3 mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark text-truncate" title="{{ $stat['camera']->name }}">{{ $stat['camera']->name }}</span>
                            <span class="badge-custom bg-light text-dark" style="font-size: 10px;">{{ $stat['camera']->brand }}</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($stat['uptime'], 2) }}%</h3>
                            <small class="text-muted">Uptime</small>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['uptime'] }}%" aria-valuenow="{{ $stat['uptime'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">Tidak ada kamera terdaftar.</div>
            @endforelse
        </div>

        <!-- Logs Table -->
        <div class="card-custom border-0 overflow-hidden">
            <div class="card-custom-header">
                <h5 class="mb-0 fw-bold">Timeline Kejadian & Status</h5>
            </div>
            <div class="card-custom-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Waktu Kejadian</th>
                                <th scope="col">Nama Kamera</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Tipe Event</th>
                                <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monitoringLogs as $log)
                                <tr>
                                    <td class="fw-semibold">{{ $log->recorded_at->format('d M Y H:i:s') }}</td>
                                    <td class="fw-bold text-dark">{{ $log->camera->name }}</td>
                                    <td>{{ $log->camera->location->name }}</td>
                                    <td>
                                        @if($log->event_type === 'online')
                                            <span class="badge-custom badge-success">Online</span>
                                        @elseif($log->event_type === 'offline')
                                            <span class="badge-custom badge-danger">Offline</span>
                                        @elseif($log->event_type === 'motion')
                                            <span class="badge-custom badge-info">Motion</span>
                                        @elseif($log->event_type === 'maintenance')
                                            <span class="badge-custom badge-warning">Maint</span>
                                        @else
                                            <span class="badge-custom badge-danger">Error</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-clock-history d-block mb-3" style="font-size: 3rem;"></i>
                                        Tidak ada catatan kejadian monitoring untuk filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 2: Keuangan (Revenue) -->
    @if($activeTab === 'revenue')
        <!-- Filters -->
        <div class="card-custom border-0 mb-4">
            <div class="card-custom-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Dari Tanggal</label>
                        <input type="date" wire:model.live="r_start_date" class="form-control form-control-custom">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Sampai Tanggal</label>
                        <input type="date" wire:model.live="r_end_date" class="form-control form-control-custom">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Klien</label>
                        <select wire:model.live="r_client_id" class="form-select form-control-custom">
                            <option value="">Semua Klien</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->company }} ({{ $c->name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats Summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card-custom border-0 p-3 mb-0" style="background-color: var(--color-surface-muted);">
                    <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase; font-weight: 600;">Total Penerbitan</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">Rp {{ number_format($revenueSummary['total_issued'], 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $revenueSummary['count_issued'] }} Invoice diterbitkan</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-custom border-0 p-3 mb-0" style="background-color: rgba(25, 135, 84, 0.05); border-left: 4px solid #198754;">
                    <span class="text-success d-block" style="font-size: 12px; text-transform: uppercase; font-weight: 600;">Total Terbayar (Lunas)</span>
                    <h3 class="fw-bold mb-0 text-success mt-1">Rp {{ number_format($revenueSummary['total_paid'], 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $revenueSummary['count_paid'] }} Invoice lunas</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-custom border-0 p-3 mb-0" style="background-color: rgba(220, 53, 69, 0.05); border-left: 4px solid #dc3545;">
                    <span class="text-danger d-block" style="font-size: 12px; text-transform: uppercase; font-weight: 600;">Total Piutang (Overdue)</span>
                    <h3 class="fw-bold mb-0 text-danger mt-1">Rp {{ number_format($revenueSummary['total_overdue'], 0, ',', '.') }}</h3>
                    <small class="text-muted">{{ $revenueSummary['count_overdue'] }} Invoice jatuh tempo</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-custom border-0 p-3 mb-0" style="background-color: rgba(108, 117, 125, 0.05); border-left: 4px solid #6c757d;">
                    <span class="text-secondary d-block" style="font-size: 12px; text-transform: uppercase; font-weight: 600;">Total Batal</span>
                    <h3 class="fw-bold mb-0 text-secondary mt-1">Rp {{ number_format($revenueSummary['total_cancelled'], 0, ',', '.') }}</h3>
                    <small class="text-muted">Invoice dibatalkan</small>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card-custom border-0 mb-4" wire:ignore>
            <div class="card-custom-header">
                <h5 class="mb-0 fw-bold">Grafik Perbandingan Tagihan vs Pembayaran</h5>
            </div>
            <div class="card-custom-body" style="height: 350px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="card-custom border-0 overflow-hidden">
            <div class="card-custom-header">
                <h5 class="mb-0 fw-bold">Detail Data Transaksi Invoice</h5>
            </div>
            <div class="card-custom-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th scope="col">No. Invoice</th>
                                <th scope="col">Klien / Instansi</th>
                                <th scope="col">Tanggal Terbit</th>
                                <th scope="col">Jatuh Tempo</th>
                                <th scope="col">Grand Total</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenueInvoices as $invoice)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $invoice->number }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $invoice->client->name }}</div>
                                        <small class="text-muted">{{ $invoice->client->company }}</small>
                                    </td>
                                    <td>{{ $invoice->issue_date->format('d M Y') }}</td>
                                    <td>{{ $invoice->due_date->format('d M Y') }}</td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
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
                                            <span class="badge-custom badge-warning">Batal</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-receipt d-block mb-3" style="font-size: 3rem;"></i>
                                        Tidak ada data invoice ditemukan untuk periode filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:initialized', () => {
                let chart = null;

                const renderChart = () => {
                    const chartData = @js($monthlyChart);
                    const labels = chartData.map(item => item.label);
                    const issued = chartData.map(item => item.issued);
                    const paid = chartData.map(item => item.paid);

                    const ctx = document.getElementById('revenueChart');
                    if (!ctx) return;

                    if (chart) {
                        chart.destroy();
                    }

                    chart = new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Total Tagihan (Rp)',
                                    data: issued,
                                    backgroundColor: 'rgba(58, 87, 232, 0.6)',
                                    borderColor: 'rgb(58, 87, 232)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Total Terbayar (Rp)',
                                    data: paid,
                                    backgroundColor: 'rgba(25, 135, 84, 0.6)',
                                    borderColor: 'rgb(25, 135, 84)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });
                };

                renderChart();

                // Listen for updates from Livewire
                Livewire.hook('commit', ({ component, succeed }) => {
                    succeed(() => {
                        // Re-render chart on tab switch or filter changes
                        renderChart();
                    });
                });
            });
        </script>
    @endif

    <!-- Tab 3: Inventaris & Stok -->
    @if($activeTab === 'inventory')
        <!-- Valuation Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card-custom border-0 p-3 mb-0">
                    <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase; font-weight: 600;">Estimasi Nilai Beli Inventaris</span>
                    <h3 class="fw-bold mb-0 text-primary mt-1">Rp {{ number_format($inventoryValuation['purchase'], 0, ',', '.') }}</h3>
                    <small class="text-muted">Total nilai pembelian berdasarkan harga pokok.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom border-0 p-3 mb-0" style="border-left: 4px solid var(--color-text-inverse);">
                    <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase; font-weight: 600;">Estimasi Nilai Jual Inventaris</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1">Rp {{ number_format($inventoryValuation['selling'], 0, ',', '.') }}</h3>
                    <small class="text-muted">Total nilai penjualan jika semua stok terjual.</small>
                </div>
            </div>
        </div>

        <!-- Warnings / Low stock items -->
        @if(count($lowStockItems) > 0)
            <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center gap-3" style="border-radius: var(--radius-sm);">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">Peringatan: Stok Barang Menipis!</h6>
                    <span>Ada {{ count($lowStockItems) }} barang yang memiliki stok di bawah level minimum. Harap segera lakukan restock.</span>
                </div>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <!-- Low Stock List -->
            <div class="col-md-4">
                <div class="card-custom border-0 overflow-hidden mb-0">
                    <div class="card-custom-header bg-warning-subtle text-warning-emphasis">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-circle"></i> Daftar Stok Menipis</h6>
                    </div>
                    <div class="card-custom-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($lowStockItems as $lowItem)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $lowItem->name }}</div>
                                        <small class="text-muted">SKU: {{ $lowItem->sku }} | Min: {{ $lowItem->min_stock }}</small>
                                    </div>
                                    <span class="badge bg-danger rounded-pill py-2 px-3 fw-bold">{{ $lowItem->stock }} {{ $lowItem->unit }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-4 text-muted">Semua stok barang dalam kondisi aman.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stock Movement Audit Logs -->
            <div class="col-md-8">
                <div class="card-custom border-0 overflow-hidden mb-0">
                    <div class="card-custom-header">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history"></i> Riwayat Aliran Keluar/Masuk Stok</h6>
                    </div>
                    <div class="card-custom-body p-0" style="max-height: 400px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-custom align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">Waktu</th>
                                        <th scope="col">Barang</th>
                                        <th scope="col">Aktivitas</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">Keterangan / Alasan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockMovements as $mv)
                                        <tr>
                                            <td style="font-size: var(--font-size-xs);">{{ $mv['timestamp']->format('d M Y H:i') }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $mv['item_name'] }}</div>
                                                <small class="text-muted">SKU: {{ $mv['sku'] }}</small>
                                            </td>
                                            <td>
                                                @if($mv['action'] === 'Masuk')
                                                    <span class="badge-custom bg-success-subtle text-success">Masuk / IN</span>
                                                @else
                                                    <span class="badge-custom bg-danger-subtle text-danger">Keluar / OUT</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold {{ $mv['action'] === 'Masuk' ? 'text-success' : 'text-danger' }}">
                                                {{ $mv['action'] === 'Masuk' ? '+' : '-' }}{{ $mv['quantity'] }}
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $mv['reason'] }}</div>
                                                <small class="text-muted">{{ $mv['notes'] }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">Tidak ada riwayat mutasi stok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 4: Kinerja Klien -->
    @if($activeTab === 'clients')
        <div class="card-custom border-0 overflow-hidden">
            <div class="card-custom-header">
                <h5 class="mb-0 fw-bold">Rekap Transaksi & Revenue Per Klien</h5>
            </div>
            <div class="card-custom-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Nama Klien / Perusahaan</th>
                                <th scope="col">Kontak / Email</th>
                                <th scope="col">Jumlah Penawaran</th>
                                <th scope="col">Jumlah Invoice</th>
                                <th scope="col">Total Pembelian (Revenue)</th>
                                <th scope="col">Sudah Terbayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientReport as $cRep)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $cRep->company ?? 'Individu' }}</div>
                                        <small class="text-muted">{{ $cRep->name }}</small>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-envelope"></i> {{ $cRep->email ?? '-' }}</div>
                                        <div><i class="bi bi-telephone"></i> {{ $cRep->phone ?? '-' }}</div>
                                    </td>
                                    <td class="fw-semibold text-center">{{ $cRep->quotations_count }}</td>
                                    <td class="fw-semibold text-center">{{ $cRep->invoices_count }}</td>
                                    <td class="fw-bold text-dark">Rp {{ number_format($cRep->total_revenue, 0, ',', '.') }}</td>
                                    <td class="fw-bold text-success">Rp {{ number_format($cRep->paid_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-people d-block mb-3" style="font-size: 3rem;"></i>
                                        Tidak ada data klien terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
