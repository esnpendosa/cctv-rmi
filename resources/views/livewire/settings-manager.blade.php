<div class="container-fluid py-4">
    <div class="row">
        <!-- Page Header -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-dark mb-1">Pengaturan Sistem & Dokumen</h4>
                    <p class="text-muted mb-0">Sesuaikan informasi Kop Surat, Tanda Tangan (TTD), Stempel, PPN, serta Format Penomoran Dokumen.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Preview Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: var(--radius-md);">
                <div class="card-header bg-light border-0 py-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-eye-fill text-primary me-2"></i>Pratinjau Kop Surat & TTD Dokumen</h6>
                </div>
                <div class="card-body p-4 bg-white border-top">
                    <!-- Live Kop Surat Preview -->
                    <div class="border p-4 rounded mb-4" style="background-color: #fafafa; max-width: 850px; margin: 0 auto; box-shadow: inset 0 0 10px rgba(0,0,0,0.05);">
                        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 5px;">
                            <tr style="border: none;">
                                <td style="width: 120px; border: none; padding: 0; vertical-align: middle; text-align: center;">
                                    @if($logoFile)
                                        <img src="{{ $logoFile->temporaryUrl() }}" style="max-height: 60px; max-width: 110px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" />
                                    @elseif($logoPath)
                                        <img src="{{ asset('storage/' . $logoPath) }}" style="max-height: 60px; max-width: 110px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" />
                                    @else
                                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #1e3c72, #2a5298); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 20px; font-family: sans-serif; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 2px solid #fff; position: relative; margin: 0 auto;">
                                            RMI
                                            <div style="position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #00d2ff; top: 5px; right: 5px;"></div>
                                            <div style="position: absolute; width: 6px; height: 6px; border-radius: 50%; background: #00d2ff; bottom: 8px; left: 8px;"></div>
                                        </div>
                                    @endif
                                </td>
                                <td style="border: none; padding: 0 0 0 15px; vertical-align: middle; text-align: left;">
                                    <h1 style="margin: 0; color: #2a5298; font-size: 18px; font-weight: 800; letter-spacing: 0.5px; font-family: Arial, sans-serif;">{{ $companyName ?: 'NAMA PERUSAHAAN' }}</h1>
                                    <p style="margin: 2px 0 4px 0; color: #777; font-size: 9px; font-style: italic; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; font-family: Arial, sans-serif;">{{ $companySlogan ?: 'SLOGAN PERUSAHAAN' }}</p>
                                    <p style="margin: 0; color: #444; font-size: 9px; font-family: Arial, sans-serif; line-height: 1.3;">{{ $companyAddress ?: 'Alamat Perusahaan' }}, Telp: {{ $companyPhone ?: 'Telepon' }}</p>
                                    <p style="margin: 1px 0 0 0; color: #444; font-size: 9px; font-family: Arial, sans-serif; line-height: 1.3;">Email: {{ $companyEmail ?: 'Email' }} | Website: {{ $companyWebsite ?: 'Website' }}</p>
                                </td>
                            </tr>
                        </table>
                        <div style="border-top: 2px solid #333; border-bottom: 0.5px solid #333; height: 2px; margin-top: 8px;"></div>
                        
                        <!-- Mini Preview of Signature -->
                        <div class="d-flex justify-content-end mt-4">
                            <div style="width: 200px; text-align: center; position: relative;">
                                <p style="margin: 0 0 55px 0; font-size: 11px; color: #666;">Hormat kami,</p>
                                
                                <div style="position: absolute; top: 10px; left: 30px; width: 140px; height: 60px; pointer-events: none; opacity: 0.85;">
                                    @if($stampSignatureFile)
                                        <img src="{{ $stampSignatureFile->temporaryUrl() }}" style="position: absolute; top: -15px; left: 15px; width: 110px; height: 75px; object-fit: contain;" />
                                    @elseif($stampSignaturePath)
                                        <img src="{{ asset('storage/' . $stampSignaturePath) }}" style="position: absolute; top: -15px; left: 15px; width: 110px; height: 75px; object-fit: contain;" />
                                    @else
                                        <!-- Stamp Layer -->
                                        @if($stampFile)
                                            <img src="{{ $stampFile->temporaryUrl() }}" style="position: absolute; top: -15px; left: 0px; width: 75px; height: 75px; object-fit: contain;" />
                                        @elseif($stampPath)
                                            <img src="{{ asset('storage/' . $stampPath) }}" style="position: absolute; top: -15px; left: 0px; width: 75px; height: 75px; object-fit: contain;" />
                                        @elseif($signatureStampEnable)
                                            <!-- Dynamic Stamp -->
                                            <svg width="80" height="80" viewBox="0 0 100 100" style="position: absolute; top: -15px; left: 0px;">
                                                <circle cx="50" cy="50" r="38" fill="none" stroke="#5a52a3" stroke-width="2.5" stroke-dasharray="1000" />
                                                <circle cx="50" cy="50" r="32" fill="none" stroke="#5a52a3" stroke-width="1" />
                                                <path id="previewStampPath" d="M 18,50 A 32,32 0 1,1 82,50" fill="none" />
                                                <text font-size="5" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3">
                                                    <textPath href="#previewStampPath" startOffset="50%" text-anchor="middle">CV. ROZITECH MULTIMEDIA</textPath>
                                                </text>
                                                <path id="previewStampPathBottom" d="M 82,50 A 32,32 0 0,1 18,50" fill="none" />
                                                <text font-size="5" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3">
                                                    <textPath href="#previewStampPathBottom" startOffset="50%" text-anchor="middle">INDONESIA - GRESIK</textPath>
                                                </text>
                                                <circle cx="50" cy="50" r="14" fill="none" stroke="#5a52a3" stroke-width="1" />
                                                <text x="50" y="53" font-size="7" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3" text-anchor="middle">RMI</text>
                                            </svg>
                                        @endif

                                        <!-- Signature Layer -->
                                        @if($signatureFile)
                                            <img src="{{ $signatureFile->temporaryUrl() }}" style="position: absolute; top: -5px; left: 15px; width: 100px; height: 60px; object-fit: contain;" />
                                        @elseif($signaturePath)
                                            <img src="{{ asset('storage/' . $signaturePath) }}" style="position: absolute; top: -5px; left: 15px; width: 100px; height: 60px; object-fit: contain;" />
                                        @else
                                            <!-- Dynamic Signature -->
                                            <svg width="100" height="60" viewBox="0 0 120 70" style="position: absolute; top: -5px; left: 15px;">
                                                <path d="{{ $signaturePathD ?: 'M10,45 Q30,10 45,35 T80,25 T105,40' }}" fill="none" stroke="#1c2c5b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        @endif
                                    @endif
                                </div>

                                <p style="margin: 0; font-size: 11px; font-weight: bold; text-decoration: underline;">{{ $signatureName ?: 'Nama Penandatangan' }}</p>
                                <p style="margin: 2px 0 0 0; font-size: 10px; color: #555;">{{ $signatureTitle ?: 'Jabatan' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form Settings -->
    <form wire:submit.prevent="saveSettings">
        <div class="row">
            <!-- Left Side: Company Letterhead & Signature Settings -->
            <div class="col-lg-8">
                <!-- Company / Letterhead Details Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-building me-2 text-primary"></i>Informasi Kop Surat</h6>
                    </div>
                    <div class="card-body p-4 border-top">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" wire:model="companyName" class="form-control @error('companyName') is-invalid @enderror" placeholder="CV. ROZITECH MULTIMEDIA INDONESIA">
                                @error('companyName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Slogan / Kategori Bisnis</label>
                                <input type="text" wire:model="companySlogan" class="form-control @error('companySlogan') is-invalid @enderror" placeholder="IT CONSULTANT | NETWORKING | IT SECURITY">
                                @error('companySlogan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Kantor <span class="text-danger">*</span></label>
                                <textarea wire:model="companyAddress" class="form-control @error('companyAddress') is-invalid @enderror" rows="2" placeholder="Jl. Desa Leran RT 01 RW 01, Manyar, Gresik"></textarea>
                                @error('companyAddress') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nomor Telepon / HP <span class="text-danger">*</span></label>
                                <input type="text" wire:model="companyPhone" class="form-control @error('companyPhone') is-invalid @enderror" placeholder="(0821) 8782 7382">
                                @error('companyPhone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Email Perusahaan <span class="text-danger">*</span></label>
                                <input type="email" wire:model="companyEmail" class="form-control @error('companyEmail') is-invalid @enderror" placeholder="rozitech.gsk@gmail.com">
                                @error('companyEmail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Website Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" wire:model="companyWebsite" class="form-control @error('companyWebsite') is-invalid @enderror" placeholder="rozitech.co.id">
                                @error('companyWebsite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 border-top pt-3">
                                <label class="form-label fw-semibold">Upload Logo Perusahaan</label>
                                <input type="file" wire:model="logoFile" class="form-control @error('logoFile') is-invalid @enderror">
                                <div wire:loading wire:target="logoFile" class="text-primary mt-1">
                                    <small><i class="bi bi-hourglass-split me-1"></i>Mengunggah logo...</small>
                                </div>
                                @error('logoFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if($logoPath)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <span class="text-muted small">Logo aktif saat ini:</span>
                                        <img src="{{ asset('storage/' . $logoPath) }}" class="img-thumbnail" style="max-height: 40px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signature & Stamp Settings Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-pen me-2 text-primary"></i>Tanda Tangan & Stempel Resmi</h6>
                    </div>
                    <div class="card-body p-4 border-top">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Penandatangan <span class="text-danger">*</span></label>
                                <input type="text" wire:model="signatureName" class="form-control @error('signatureName') is-invalid @enderror" placeholder="Fachrur Rozi, S.Kom">
                                @error('signatureName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jabatan / Gelar <span class="text-danger">*</span></label>
                                <input type="text" wire:model="signatureTitle" class="form-control @error('signatureTitle') is-invalid @enderror" placeholder="Direktur Utama">
                                @error('signatureTitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Combined Stamp + TTD Upload -->
                            <div class="col-12 border-top pt-3">
                                <div class="alert alert-info py-2 px-3 mb-3">
                                    <small><i class="bi bi-info-circle-fill me-1"></i> <strong>Tips:</strong> Jika Anda memiliki stempel dan tanda tangan yang sudah digabung dalam satu file gambar (seperti scan/foto lembaran berstempel & ttd), unggahlah di bawah ini.</small>
                                </div>
                                <label class="form-label fw-semibold">Upload Gambar TTD & Stempel (Jadi Satu / Gabungan)</label>
                                <input type="file" wire:model="stampSignatureFile" class="form-control @error('stampSignatureFile') is-invalid @enderror">
                                <div wire:loading wire:target="stampSignatureFile" class="text-primary mt-1">
                                    <small><i class="bi bi-hourglass-split me-1"></i>Mengunggah berkas...</small>
                                </div>
                                @error('stampSignatureFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if($stampSignaturePath)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <span class="text-muted small">TTD & Stempel gabungan saat ini:</span>
                                        <img src="{{ asset('storage/' . $stampSignaturePath) }}" class="img-thumbnail" style="max-height: 50px;">
                                    </div>
                                @endif
                            </div>

                            <!-- Separate Uploads (Collapsed or Alternative) -->
                            <div class="col-12 mt-3">
                                <div class="accordion" id="accordionSeparateFiles">
                                    <div class="accordion-item border">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeparate" aria-expanded="false" aria-controls="collapseSeparate">
                                                <small class="fw-semibold text-muted">Atau unggah file TTD & Stempel secara terpisah (opsional)</small>
                                            </button>
                                        </h2>
                                        <div id="collapseSeparate" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionSeparateFiles">
                                            <div class="accordion-body bg-light p-3">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Upload Gambar Tanda Tangan (Terpisah)</label>
                                                        <input type="file" wire:model="signatureFile" class="form-control @error('signatureFile') is-invalid @enderror">
                                                        @error('signatureFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        @if($signaturePath)
                                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                                <img src="{{ asset('storage/' . $signaturePath) }}" class="img-thumbnail" style="max-height: 40px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Upload Gambar Stempel (Terpisah)</label>
                                                        <input type="file" wire:model="stampFile" class="form-control @error('stampFile') is-invalid @enderror">
                                                        @error('stampFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        @if($stampPath)
                                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                                <img src="{{ asset('storage/' . $stampPath) }}" class="img-thumbnail" style="max-height: 40px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 border-top pt-3">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="stampSwitch" wire:model="signatureStampEnable">
                                    <label class="form-check-label fw-semibold" for="stampSwitch">Aktifkan Stempel Bulat Otomatis (Jika tidak mengunggah file gambar stempel)</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Path SVG Tanda Tangan (Fallback Handwritten Line)</label>
                                <input type="text" wire:model="signaturePathD" class="form-control @error('signaturePathD') is-invalid @enderror" placeholder="M10,45 Q30,10 45,35 T80,25 T105,40 M45,35 Q60,5 75,55">
                                <small class="text-muted d-block mt-1">Hanya digunakan sebagai fallback jika file gambar TTD kosong.</small>
                                @error('signaturePathD') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: System Configs & Save Button -->
            <div class="col-lg-4">
                <!-- System Config Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-sliders me-2 text-primary"></i>Parameter Keuangan</h6>
                    </div>
                    <div class="card-body p-4 border-top">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tarif PPN (%) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" wire:model="defaultTaxRate" class="form-control @error('defaultTaxRate') is-invalid @enderror" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('defaultTaxRate') <div class="text-danger mt-1 fs-6">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Prefix No. Penawaran <span class="text-danger">*</span></label>
                                <input type="text" wire:model="quotationPrefix" class="form-control @error('quotationPrefix') is-invalid @enderror" placeholder="QUO">
                                @error('quotationPrefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Prefix No. Invoice <span class="text-danger">*</span></label>
                                <input type="text" wire:model="invoicePrefix" class="form-control @error('invoicePrefix') is-invalid @enderror" placeholder="INV">
                                @error('invoicePrefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Action Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--radius-md);">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
