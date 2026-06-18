<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Penawaran {{ $quotation->number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            margin: 0;
            padding: 30px 40px;
            font-size: 13px;
            line-height: 1.5;
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
        <button onclick="window.print()" style="background-color: #2a5298; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; font-family: Arial, sans-serif;">Cetak Penawaran</button>
    </div>

    <!-- Date, Helper & Settings Loading -->
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

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $dateObj = $quotation->created_at ?? now();
        
        // Extract city from address if possible
        $city = 'Gresik';
        if (preg_match('/Manyar,\s*([A-Za-z]+)/', $companyAddress, $matches)) {
            $city = $matches[1];
        } elseif (preg_match('/Gresik/i', $companyAddress)) {
            $city = 'Gresik';
        }
        
        $formattedDate = $city . ', ' . $dateObj->format('d') . ' ' . $months[(int)$dateObj->format('m')] . ' ' . $dateObj->format('Y');
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
    
    <!-- Double Horizontal Line underneath letterhead -->
    <div style="border-top: 2px solid #333; border-bottom: 0.5px solid #333; height: 2px; margin-top: 8px; margin-bottom: 20px;"></div>

    <!-- Meta Fields -->
    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 20px;">
        <tr style="border: none;">
            <td style="border: none; width: 60%; padding: 0; vertical-align: top;">
                <table style="border-collapse: collapse; border: none; margin: 0; padding: 0;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; width: 80px; font-size: 13px;">Nomor</td>
                        <td style="border: none; padding: 2px 5px; width: 10px; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; font-size: 13px; font-weight: bold;">{{ $quotation->number }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; font-size: 13px;">Perihal</td>
                        <td style="border: none; padding: 2px 5px; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; font-size: 13px;">Penawaran Pekerjaan Instalasi CCTV</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 2px 0; font-size: 13px;">Lampiran</td>
                        <td style="border: none; padding: 2px 5px; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; font-size: 13px;">-</td>
                    </tr>
                </table>
            </td>
            <td style="border: none; width: 40%; padding: 0; text-align: right; vertical-align: top; font-size: 13px;">
                {{ $formattedDate }}
            </td>
        </tr>
    </table>

    <!-- Yth Receiver Section -->
    <div style="font-size: 13px; margin-bottom: 20px; line-height: 1.4;">
        <p style="margin: 0 0 5px 0;">Yth.</p>
        <p style="margin: 0 0 2px 0; font-weight: bold;">{{ $quotation->client->name }}</p>
        @if($quotation->client->company)
            <p style="margin: 0 0 2px 0; font-weight: bold;">{{ $quotation->client->company }}</p>
        @endif
        <p style="margin: 0;">di Tempat</p>
    </div>

    <!-- Opening Text -->
    <div style="font-size: 13px; text-align: justify; line-height: 1.5; margin-bottom: 15px;">
        <p style="margin: 0 0 10px 0;">Dengan Hormat,</p>
        <p style="margin: 0 0 10px 0;">Bersama surat penawaran ini kami dari {{ $companyName }} selaku jasa all in one IT services bermaksud memberikan penawaran Pekerjaan Instalasi CCTV.</p>
        <p style="margin: 0;">Adapun spesifikasi yang akan kami ajukan sebagai berikut:</p>
    </div>

    <!-- Items Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; border: 1.5px solid #000;">
        <thead>
            <tr style="background-color: #fff; border-bottom: 1.5px solid #000;">
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 12px; font-weight: bold; width: 40px;">No</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: left; font-size: 12px; font-weight: bold;">Description</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 12px; font-weight: bold; width: 60px;">Qty</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 12px; font-weight: bold; width: 140px;">Unit Price</th>
                <th style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 12px; font-weight: bold; width: 140px;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
                <tr style="border-bottom: 1.5px solid #000;">
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 12px;">{{ $index + 1 }}</td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; font-size: 12px; line-height: 1.4;">
                        <strong>{{ $item->inventory ? $item->inventory->name : 'Item' }}</strong>
                        @if($item->description)
                            <br><small style="color: #444;">{{ $item->description }}</small>
                        @endif
                    </td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: center; font-size: 12px;">
                        {{ $item->qty }} {{ $item->inventory?->unit ?? 'Pcs' }}
                    </td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 12px;">
                        <span style="float: left;">Rp</span>{{ number_format($item->unit_price, 0, ',', '.') }}
                    </td>
                    <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 12px;">
                        <span style="float: left;">Rp</span>{{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            <!-- Grand Total Row -->
            <tr style="font-weight: bold;">
                <td colspan="4" style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 12px;">Grand Total</td>
                <td style="border: 1.5px solid #000; padding: 6px 8px; text-align: right; font-size: 12px; background-color: #fff;">
                    <span style="float: left;">Rp</span>{{ number_format($quotation->total, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Notes & Conditions -->
    @if($quotation->notes)
        <div style="font-size: 11px; font-style: italic; line-height: 1.4; margin-bottom: 25px; color: #333;">
            <p style="margin: 0; white-space: pre-line;">{{ $quotation->notes }}</p>
        </div>
    @else
        <div style="font-size: 11px; font-style: italic; line-height: 1.4; margin-bottom: 25px; color: #333;">
            <p style="margin: 0;">* Garansi dan free kunjungan 4 kali dalam 40 hari setelah proses instalasi selesai.</p>
        </div>
    @endif

    <!-- Closing Paragraph -->
    <div style="font-size: 13px; line-height: 1.5; margin-bottom: 30px;">
        <p style="margin: 0;">Demikian surat penawaran ini kami ajukan. Atas perhatian dan kepercayaannya selama ini, kami ucapkan terima kasih.</p>
    </div>

    <!-- Signature block -->
    <div style="margin-top: 40px; display: flex; justify-content: flex-end; page-break-inside: avoid;">
        <div style="width: 250px; text-align: center; position: relative;">
            <p style="margin: 0 0 65px 0; font-size: 13px;">Hormat kami,</p>
            
            <!-- Stylized Stamp and Signature Graphic -->
            <div style="position: absolute; top: 15px; left: 50px; width: 150px; height: 80px; pointer-events: none; opacity: 0.85;">
                @if($stampSignaturePath)
                    <img src="{{ asset('storage/' . $stampSignaturePath) }}" style="position: absolute; top: -15px; left: 15px; width: 110px; height: 75px; object-fit: contain;" />
                @else
                    @if($stampPath)
                        <img src="{{ asset('storage/' . $stampPath) }}" style="position: absolute; top: -15px; left: 0px; width: 80px; height: 80px; object-fit: contain;" />
                    @elseif($signatureStampEnable)
                        <!-- Stamp Circle -->
                        <svg width="100" height="100" viewBox="0 0 100 100" style="position: absolute; top: -10px; left: 0px;">
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#5a52a3" stroke-width="2.5" stroke-dasharray="1000" />
                            <circle cx="50" cy="50" r="32" fill="none" stroke="#5a52a3" stroke-width="1" />
                            <!-- Stamp Text -->
                            <path id="stampPath" d="M 18,50 A 32,32 0 1,1 82,50" fill="none" />
                            <text font-size="5" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3">
                                <textPath href="#stampPath" startOffset="50%" text-anchor="middle">{{ Str::limit(strtoupper($companyName), 22, '') }}</textPath>
                            </text>
                            <path id="stampPathBottom" d="M 82,50 A 32,32 0 0,1 18,50" fill="none" />
                            <text font-size="5" font-family="Arial, sans-serif" font-weight="bold" fill="#5a52a3">
                                <textPath href="#stampPathBottom" startOffset="50%" text-anchor="middle">INDONESIA - {{ strtoupper($city) }}</textPath>
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
                        <svg width="120" height="70" viewBox="0 0 120 70" style="position: absolute; top: 0px; left: 15px;">
                            <path d="{{ $signaturePathD }}" fill="none" stroke="#1c2c5b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @endif
                @endif
            </div>

            <p style="margin: 0; font-size: 13px; font-weight: bold; text-decoration: underline;">{{ $signatureName }}</p>
            <p style="margin: 3px 0 0 0; font-size: 12px; color: #555;">{{ $signatureTitle }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
