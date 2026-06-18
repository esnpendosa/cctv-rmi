<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            margin: 0;
            padding: 30px 40px;
            font-size: 13px;
            line-height: 1.5;
            position: relative;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Action Trigger -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="background-color: #5b9bd5; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; font-family: Arial, sans-serif;">Cetak Invoice</button>
    </div>

    <!-- Load Dynamic Company Settings -->
    @php
        $companyName = \App\Models\Setting::get('company_name', 'CV. ROZITECH MULTIMEDIA INDONESIA');
        $companySlogan = \App\Models\Setting::get('company_slogan', 'IT CONSULTANT | NETWORKING | IT SECURITY');
        $companyAddress = \App\Models\Setting::get('company_address', 'Jl. Desa Leran RT 01 RW 01, Manyar, Gresik');
        $companyPhone = \App\Models\Setting::get('company_phone', '(0821) 8782 7382, (0856) 0411 8932');
        $companyEmail = \App\Models\Setting::get('company_email', 'rozitech.gsk@gmail.com');
        $companyWebsite = \App\Models\Setting::get('company_website', 'rozitech.co.id');

        $logoPath = \App\Models\Setting::get('company_logo_path');
        $stampPath = \App\Models\Setting::get('company_stamp_path');
        $signaturePath = \App\Models\Setting::get('company_signature_path');
        $stampSignaturePath = \App\Models\Setting::get('company_stamp_signature_path');

        $signatureName = \App\Models\Setting::get('signature_name', 'Fachrur Rozi, S.Kom');
        $signatureTitle = \App\Models\Setting::get('signature_title', 'Direktur Utama');
        $signatureStampEnable = (bool) \App\Models\Setting::get('signature_stamp_enable', '1');
        $signaturePathD = \App\Models\Setting::get('signature_path_d', 'M10,45 Q30,10 45,35 T80,25 T105,40 M45,35 Q60,5 75,55');

        // Extract initials from company name for the logo
        $words = explode(' ', str_replace(['CV.', 'PT.', 'UD.', 'CV', 'PT', 'UD'], '', $companyName));
        $initials = '';
        foreach ($words as $word) {
            $trimmed = trim($word);
            if (!empty($trimmed)) {
                $initials .= strtoupper($trimmed[0]);
            }
            if (strlen($initials) >= 3) break;
        }
        if (empty($initials)) {
            $initials = 'RMI';
        }

        $dateObj = $invoice->issue_date ?? now();
        $formattedDate = $dateObj->format('d') . ' / ' . $dateObj->format('m') . ' / ' . $dateObj->format('Y');

        $dueDays = $invoice->issue_date && $invoice->due_date ? $invoice->issue_date->diffInDays($invoice->due_date) : 1;
    @endphp

    <!-- Kop Surat (Letterhead) -->
    <table class="kop-table" style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 5px;">
        <tr style="border: none;">
            <td style="width: 120px; border: none; padding: 0; vertical-align: middle; text-align: center;">
                @if($logoPath)
                    <img src="{{ asset('storage/' . $logoPath) }}" style="max-height: 60px; max-width: 110px;" />
                @else
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #1e3c72, #2a5298); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 20px; font-family: sans-serif; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 2px solid #fff; position: relative; margin: 0 auto;">
                        {{ $initials }}
                        <div style="position: absolute; width: 10px; height: 10px; border-radius: 50%; background: #00d2ff; top: 5px; right: 5px;"></div>
                        <div style="position: absolute; width: 6px; height: 6px; border-radius: 50%; background: #00d2ff; bottom: 8px; left: 8px;"></div>
                    </div>
                @endif
            </td>
            <td style="border: none; padding: 0 0 0 15px; vertical-align: middle; text-align: left;">
                <h1 style="margin: 0; color: #2a5298; font-size: 20px; font-weight: 800; letter-spacing: 0.5px; font-family: Arial, sans-serif;">{{ $companyName }}</h1>
                <p style="margin: 2px 0 4px 0; color: #777; font-size: 10px; font-style: italic; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; font-family: Arial, sans-serif;">{{ $companySlogan }}</p>
                <p style="margin: 0; color: #444; font-size: 10px; font-family: Arial, sans-serif; line-height: 1.3;">{{ $companyAddress }}, Telp: {{ $companyPhone }}</p>
                <p style="margin: 2px 0 0 0; color: #444; font-size: 10px; font-family: Arial, sans-serif; line-height: 1.3;">Email: {{ $companyEmail }} | Website: {{ $companyWebsite }}</p>
            </td>
        </tr>
    </table>
    
    <!-- Underline Letterhead -->
    <div style="border-top: 2px solid #333; border-bottom: 0.5px solid #333; height: 2px; margin-top: 8px; margin-bottom: 25px;"></div>

    <!-- Title & Meta Header Block -->
    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 25px;">
        <tr style="border: none;">
            <!-- Left Side: Invoice title & Bill To -->
            <td style="border: none; width: 55%; padding: 0; vertical-align: top;">
                <h2 style="font-size: 28px; font-weight: bold; margin: 0 0 15px 0; letter-spacing: 1px; color: #000; font-family: Arial, sans-serif;">INVOICE</h2>
                
                <div style="background-color: #5b9bd5; color: white; font-weight: bold; padding: 4px 8px; font-size: 11px; width: 140px; margin-bottom: 8px;">
                    Bill To:
                </div>
                <div style="font-size: 13px; line-height: 1.4;">
                    <p style="margin: 0 0 2px 0; font-weight: bold; text-transform: uppercase;">{{ $invoice->client->name }}</p>
                    @if($invoice->client->company)
                        <p style="margin: 0 0 2px 0; font-weight: bold; text-transform: uppercase;">{{ $invoice->client->company }}</p>
                    @endif
                    <p style="margin: 0; color: #444;">{{ $invoice->client->address }}</p>
                </div>
            </td>
            <!-- Right Side: Invoice metadata -->
            <td style="border: none; width: 45%; padding: 0 0 0 20px; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse; border: none; font-size: 11px;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 3px 0; width: 100px; color: #333;">No. Invoice</td>
                        <td style="border: none; padding: 3px 5px; width: 10px;">:</td>
                        <td style="border: none; padding: 3px 0; font-weight: bold;">{{ $invoice->number }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 3px 0; color: #333;">Date</td>
                        <td style="border: none; padding: 3px 5px;">:</td>
                        <td style="border: none; padding: 3px 0;">{{ $formattedDate }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 3px 0; color: #333;">No. SPK / PO</td>
                        <td style="border: none; padding: 3px 5px;">:</td>
                        <td style="border: none; padding: 3px 0;">-</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 3px 0; color: #333;">TERMS</td>
                        <td style="border: none; padding: 3px 5px;">:</td>
                        <td style="border: none; padding: 3px 0;">{{ $dueDays }} Days</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Watermark ORIGINAL (Rotated behind table) -->
    <div style="position: absolute; top: 40%; left: 32%; transform: rotate(-12deg); font-size: 65px; font-weight: 900; color: rgba(0, 0, 0, 0.04); letter-spacing: 8px; pointer-events: none; font-family: 'Arial Black', Impact, sans-serif; border: 8px solid rgba(0, 0, 0, 0.04); padding: 5px 15px; border-radius: 8px; text-transform: uppercase; z-index: 1;">
        ORIGINAL
    </div>

    <!-- Table of Items -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1.5px solid #000; position: relative; z-index: 2; background-color: transparent;">
        <thead>
            <tr style="background-color: #5b9bd5; color: white; border-bottom: 1.5px solid #000;">
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 11px; font-weight: bold; width: 40px; color: white;">No.</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: left; font-size: 11px; font-weight: bold; color: white;">Description</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 11px; font-weight: bold; width: 50px; color: white;">QTY</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 11px; font-weight: bold; width: 50px; color: white;">Unit</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 11px; font-weight: bold; width: 120px; color: white;">Unit Price</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 11px; font-weight: bold; width: 130px; color: white;">Total Price (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr style="border-bottom: 1.5px solid #000;">
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 11px;">{{ $index + 1 }}</td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; font-size: 11px; line-height: 1.4;">
                        <strong>{{ $item->inventory ? $item->inventory->name : 'Item' }}</strong>
                        @if($item->description)
                            <br><small style="color: #444;">{{ $item->description }}</small>
                        @endif
                    </td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 11px;">
                        {{ $item->qty }}
                    </td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 11px; text-transform: lowercase;">
                        {{ $item->inventory?->unit ?? 'pcs' }}
                    </td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 11px;">
                        <span style="float: left; color: #555;">Rp</span>{{ number_format($item->unit_price, 0, ',', '.') }}
                    </td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 11px;">
                        <span style="float: left; color: #555;">Rp</span>{{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer Summary Block -->
    <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 15px;">
        <tr style="border: none;">
            <!-- Left Side: Notes & Bank Info -->
            <td style="border: none; width: 60%; padding: 0; vertical-align: top;">
                <!-- Special notes and instruction Box -->
                <div style="border: 1.5px solid #000; margin-bottom: 25px; max-width: 450px;">
                    <div style="background-color: #5b9bd5; color: white; font-weight: bold; padding: 4px 8px; font-size: 11px; border-bottom: 1.5px solid #000;">
                        Special notes and instruction :
                    </div>
                    <div style="padding: 8px; font-size: 11px; line-height: 1.4; color: #111;">
                        @if($invoice->notes)
                            <div style="white-space: pre-line;">{{ $invoice->notes }}</div>
                        @else
                            <div style="margin: 0;">Layanan akses Internet, IP Public, dan server dipinjamkan selama masa penggunaan layanan.</div>
                            <div style="margin: 2px 0;">Tagihan periode berikutnya dikenakan biaya internet 2 layanan sebesar Rp200.000 dan sewa IP Public & server sebesar Rp70.000.</div>
                            <div style="margin: 2px 0;">Total tagihan bulan berikutnya mulai bulan Juli sebesar Rp270.000.</div>
                            <div style="margin: 2px 0;">Pembayaran tagihan dilakukan paling lambat tanggal 10 setiap bulan.</div>
                        @endif
                    </div>
                </div>

                <!-- Transfer Info -->
                <div style="font-size: 11px; line-height: 1.4;">
                    <p style="margin: 0 0 3px 0; font-weight: bold; color: #333;">Transfer Via:</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">FACHRUR ROZI</p>
                    <table style="border-collapse: collapse; border: none; margin: 0 0 8px 0; font-size: 11px; width: auto;">
                        <tr style="border: none;">
                            <td style="border: none; padding: 1px 0; width: 45px;">BRI</td>
                            <td style="border: none; padding: 1px 5px; width: 10px;">:</td>
                            <td style="border: none; padding: 1px 0; font-weight: bold;">621001013663537</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; padding: 1px 0; width: 45px;">BCA</td>
                            <td style="border: none; padding: 1px 5px; width: 10px;">:</td>
                            <td style="border: none; padding: 1px 0; font-weight: bold;">7415234155</td>
                        </tr>
                    </table>

                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">Rozitech Multimedia Indonesia</p>
                    <table style="border-collapse: collapse; border: none; margin: 0; font-size: 11px; width: auto;">
                        <tr style="border: none;">
                            <td style="border: none; padding: 1px 0; width: 45px;">BCA</td>
                            <td style="border: none; padding: 1px 5px; width: 10px;">:</td>
                            <td style="border: none; padding: 1px 0; font-weight: bold;">741591111</td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- Right Side: Totals Box -->
            <td style="border: none; width: 40%; padding: 0; vertical-align: top; text-align: right;">
                <table style="width: 260px; float: right; border-collapse: collapse; border: 1.5px solid #000; font-size: 11px;">
                    <tr style="border-bottom: 1.5px solid #000;">
                        <td style="border: none; padding: 6px 8px; text-align: left; font-weight: bold; width: 110px;">Sub Total</td>
                        <td style="border: none; padding: 6px 8px; text-align: right;">
                            Rp{{ number_format($invoice->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr style="border-bottom: 1.5px solid #000;">
                        <td style="border: none; padding: 6px 8px; text-align: left; color: #555;">Down Payment</td>
                        <td style="border: none; padding: 6px 8px; text-align: right;">
                            Rp0
                        </td>
                    </tr>
                    <tr style="border-bottom: 1.5px solid #000;">
                        <td style="border: none; padding: 6px 8px; text-align: left; color: #555;">Discount</td>
                        <td style="border: none; padding: 6px 8px; text-align: right;">
                            -Rp{{ number_format($invoice->discount_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr style="font-weight: bold; background-color: #fff;">
                        <td style="border: none; padding: 6px 8px; text-align: left; font-size: 12px;">Total</td>
                        <td style="border: none; padding: 6px 8px; text-align: right; font-size: 12px;">
                            Rp{{ number_format($invoice->total, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Signature Section -->
    <div style="margin-top: 55px; display: flex; justify-content: flex-end; page-break-inside: avoid;">
        <div style="width: 250px; text-align: center; position: relative;">
            <p style="margin: 0; font-size: 11px; color: #555;">{{ strtoupper($companyName) }}</p>
            <p style="margin: 5px 0 65px 0; font-size: 12px; font-weight: bold;">Hormat kami,</p>
            
            <!-- Stylized Stamp and Signature Graphic -->
            <div style="position: absolute; top: 35px; left: 50px; width: 150px; height: 80px; pointer-events: none; opacity: 0.85;">
                @if($stampSignaturePath)
                    <img src="{{ asset('storage/' . $stampSignaturePath) }}" style="position: absolute; top: -15px; left: 15px; width: 110px; height: 75px; object-fit: contain;" />
                @else
                    @if($stampPath)
                        <img src="{{ asset('storage/' . $stampPath) }}" style="position: absolute; top: -15px; left: 0px; width: 80px; height: 80px; object-fit: contain;" />
                    @elseif($signatureStampEnable)
                        <!-- Stamp Circle -->
                        <svg width="100" height="100" viewBox="0 0 100 100" style="position: absolute; top: -15px; left: 0px;">
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#5a52a3" stroke-width="2.5" stroke-dasharray="1000" />
                            <circle cx="50" cy="50" r="32" fill="none" stroke="#5a52a3" stroke-width="1" />
                            <!-- Stamp Text -->
                            <path id="stampPath" d="M 18,50 A 32,32 0 1,1 82,50" fill="none" />
                            <text font-size="5" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3">
                                <textPath href="#stampPath" startOffset="50%" text-anchor="middle">{{ Str::limit(strtoupper($companyName), 22, '') }}</textPath>
                            </text>
                            <path id="stampPathBottom" d="M 82,50 A 32,32 0 0,1 18,50" fill="none" />
                            <text font-size="5" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3">
                                <textPath href="#stampPathBottom" startOffset="50%" text-anchor="middle">INDONESIA - GRESIK</textPath>
                            </text>
                            <!-- Inner Logo -->
                            <circle cx="50" cy="50" r="14" fill="none" stroke="#5a52a3" stroke-width="1" />
                            <text x="50" y="53" font-size="7" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3" text-anchor="middle">{{ $initials }}</text>
                        </svg>
                    @endif

                    @if($signaturePath)
                        <img src="{{ asset('storage/' . $signaturePath) }}" style="position: absolute; top: -5px; left: 15px; width: 100px; height: 60px; object-fit: contain;" />
                    @else
                        <!-- Blue Handwritten Signature Line -->
                        <svg width="120" height="70" viewBox="0 0 120 70" style="position: absolute; top: -5px; left: 15px;">
                            <path d="{{ $signaturePathD }}" fill="none" stroke="#1c2c5b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @endif
                @endif
            </div>

            <p style="margin: 0; font-size: 12px; font-weight: bold; text-decoration: underline;">{{ $signatureName }}</p>
            <p style="margin: 3px 0 0 0; font-size: 11px; color: #555;">Director</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
