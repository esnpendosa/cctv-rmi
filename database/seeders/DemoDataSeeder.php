<?php

namespace Database\Seeders;

use App\Enums\CameraAccess;
use App\Enums\CameraStatus;
use App\Enums\InventoryCondition;
use App\Enums\InvoiceStatus;
use App\Enums\QuotationStatus;
use App\Models\Camera;
use App\Models\CameraCategory;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Location;
use App\Models\MonitoringLog;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Class DemoDataSeeder
 * 
 * Seeds realistic demonstration data for development.
 * 
 * @package Database\Seeders
 */
class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        if (!$adminUser) {
            return;
        }

        // 1. Create 3 Clients
        $clientsData = [
            [
                'name' => 'Budi Santoso',
                'company' => 'PT. Sinar Mas Utama',
                'email' => 'budi.santoso@sinarmas.co.id',
                'phone' => '081234567890',
                'address' => 'Jl. Jend. Sudirman Kav. 21, Jakarta Selatan',
                'npwp' => '01.234.567.8-012.000',
                'notes' => 'Client VIP untuk instalasi CCTV gedung bertingkat.',
            ],
            [
                'name' => 'Siti Rahma',
                'company' => 'CV. Jaya Sentosa',
                'email' => 'siti.rahma@jayasentosa.com',
                'phone' => '082198765432',
                'address' => 'Jl. Gajah Mada No. 120, Jakarta Barat',
                'npwp' => '02.345.678.9-023.000',
                'notes' => 'Klien ritel untuk area parkir dan gudang.',
            ],
            [
                'name' => 'David Lee',
                'company' => 'PT. Global Technology',
                'email' => 'david.lee@globaltech.id',
                'phone' => '087755554444',
                'address' => 'Kawasan Industri Pulogadung Blok B3, Jakarta Timur',
                'npwp' => '03.456.789.0-034.000',
                'notes' => 'Klien korporat area pabrik.',
            ],
        ];

        $clients = [];
        foreach ($clientsData as $c) {
            $clients[] = Client::create($c);
        }

        // 2. Create 2 Locations per Client (Total 6)
        $locations = [];
        $locIndex = 0;
        // Jakarta centers
        $coordinates = [
            ['-6.2088', '106.8456'], // PT. Sinar Mas - Kantor Pusat
            ['-6.2100', '106.8400'], // PT. Sinar Mas - Kantor Cabang
            ['-6.2050', '106.8500'], // CV. Jaya Sentosa - Gudang A
            ['-6.2150', '106.8350'], // CV. Jaya Sentosa - Ruko Parkir
            ['-6.2000', '106.8450'], // PT. Global Tech - Pabrik 1
            ['-6.2200', '106.8550'], // PT. Global Tech - Pabrik 2
        ];

        foreach ($clients as $client) {
            for ($i = 1; $i <= 2; $i++) {
                $coord = $coordinates[$locIndex] ?? ['-6.2088', '106.8456'];
                $locations[] = Location::create([
                    'name' => $client->company . ' - Site ' . $i,
                    'address' => 'Lokasi Site ' . $i . ' milik ' . $client->company,
                    'client_id' => $client->id,
                    'latitude' => $coord[0],
                    'longitude' => $coord[1],
                    'description' => 'Instalasi area ' . ($i == 1 ? 'Utama' : 'Backup'),
                ]);
                $locIndex++;
            }
        }

        // 3. Categories reference
        $categories = CameraCategory::all();
        if ($categories->isEmpty()) {
            return;
        }

        // 4. Create 8 Cameras
        $camerasData = [
            [
                'name' => 'CCTV Lobby Utama',
                'brand' => 'Hikvision',
                'model' => 'DS-2CD2143G0-I',
                'ip_address' => '192.168.10.11',
                'rtsp_url' => 'rtsp://admin:Hik12345@192.168.10.11:554/Streaming/Channels/101',
                'stream_key' => 'lobby_utama',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Private,
                'status' => CameraStatus::Online,
                'category_slug' => 'lobby',
                'location_index' => 0,
                'lat_offset' => 0.0,
                'lng_offset' => 0.0,
                'public_token' => null,
            ],
            [
                'name' => 'CCTV Parkir Depan',
                'brand' => 'Dahua',
                'model' => 'DH-IPC-HFW2431S-S-S2',
                'ip_address' => '192.168.10.12',
                'rtsp_url' => 'rtsp://admin:Dah12345@192.168.10.12:554/cam/realmonitor?channel=1&subtype=0',
                'stream_key' => 'parkir_depan',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Public,
                'status' => CameraStatus::Online,
                'category_slug' => 'parkir',
                'location_index' => 0,
                'lat_offset' => 0.0003,
                'lng_offset' => -0.0004,
                'public_token' => (string) Str::uuid(),
            ],
            [
                'name' => 'CCTV Pintu Masuk Gedung',
                'brand' => 'Uniview',
                'model' => 'IPC2122LR3-PF40-D',
                'ip_address' => '192.168.11.15',
                'rtsp_url' => 'rtsp://admin:Uni12345@192.168.11.15:554/video1',
                'stream_key' => 'pintu_masuk_gedung',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Private,
                'status' => CameraStatus::Offline,
                'category_slug' => 'gedung',
                'location_index' => 1,
                'lat_offset' => 0.0,
                'lng_offset' => 0.0,
                'public_token' => null,
            ],
            [
                'name' => 'CCTV Koridor Utama',
                'brand' => 'Hikvision',
                'model' => 'DS-2CD1123G0-I',
                'ip_address' => '192.168.11.16',
                'rtsp_url' => 'rtsp://admin:Hik12345@192.168.11.16:554/Streaming/Channels/101',
                'stream_key' => 'koridor_utama',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Private,
                'status' => CameraStatus::Online,
                'category_slug' => 'indoor',
                'location_index' => 1,
                'lat_offset' => -0.0005,
                'lng_offset' => 0.0002,
                'public_token' => null,
            ],
            [
                'name' => 'CCTV Gudang Utama',
                'brand' => 'Ezviz',
                'model' => 'CS-C8W-A0-1H2WKFL',
                'ip_address' => '192.168.20.10',
                'rtsp_url' => 'rtsp://admin:Ez123456@192.168.20.10:554/h264/ch1/main/av_stream',
                'stream_key' => 'gudang_utama',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Private,
                'status' => CameraStatus::Online,
                'category_slug' => 'gedung',
                'location_index' => 2,
                'lat_offset' => 0.0,
                'lng_offset' => 0.0,
                'public_token' => null,
            ],
            [
                'name' => 'CCTV Parkir Karyawan',
                'brand' => 'Dahua',
                'model' => 'DH-IPC-HDW1230T1P-S4',
                'ip_address' => '192.168.21.20',
                'rtsp_url' => 'rtsp://admin:Dah12345@192.168.21.20:554/cam/realmonitor?channel=1&subtype=0',
                'stream_key' => 'parkir_karyawan',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Public,
                'status' => CameraStatus::Maintenance,
                'category_slug' => 'parkir',
                'location_index' => 3,
                'lat_offset' => 0.0,
                'lng_offset' => 0.0,
                'public_token' => (string) Str::uuid(),
            ],
            [
                'name' => 'CCTV Jalan Raya Depan Pabrik',
                'brand' => 'Imou',
                'model' => 'Cruiser SE 4MP',
                'ip_address' => '192.168.30.50',
                'rtsp_url' => 'rtsp://admin:Imou12345@192.168.30.50:554/cam/realmonitor?channel=1&subtype=0',
                'stream_key' => 'jalan_depan_pabrik',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Public,
                'status' => CameraStatus::Online,
                'category_slug' => 'jalan-raya',
                'location_index' => 4,
                'lat_offset' => 0.0,
                'lng_offset' => 0.0,
                'public_token' => (string) Str::uuid(),
            ],
            [
                'name' => 'CCTV Pagar Keliling',
                'brand' => 'Bardi',
                'model' => 'PTZ Outdoor Dome',
                'ip_address' => '192.168.31.60',
                'rtsp_url' => 'rtsp://admin:Bar12345@192.168.31.60:554/live/ch0',
                'stream_key' => 'pagar_keliling',
                'stream_type' => 'rtsp',
                'access' => CameraAccess::Private,
                'status' => CameraStatus::Online,
                'category_slug' => 'outdoor',
                'location_index' => 5,
                'lat_offset' => 0.0,
                'lng_offset' => 0.0,
                'public_token' => null,
            ],
        ];

        $cameras = [];
        foreach ($camerasData as $cam) {
            $cat = $categories->where('slug', $cam['category_slug'])->first();
            $loc = $locations[$cam['location_index']];

            $cameras[] = Camera::create([
                'name' => $cam['name'],
                'brand' => $cam['brand'],
                'model' => $cam['model'],
                'ip_address' => $cam['ip_address'],
                'rtsp_url' => $cam['rtsp_url'],
                'stream_key' => $cam['stream_key'],
                'stream_type' => $cam['stream_type'],
                'access' => $cam['access'],
                'status' => $cam['status'],
                'category_id' => $cat->id,
                'location_id' => $loc->id,
                'latitude' => floatval($loc->latitude) + $cam['lat_offset'],
                'longitude' => floatval($loc->longitude) + $cam['lng_offset'],
                'installation_date' => Carbon::now()->subMonths(6),
                'warranty_until' => Carbon::now()->addMonths(18),
                'last_online_at' => $cam['status'] == CameraStatus::Online ? Carbon::now()->subMinutes(10) : Carbon::now()->subDays(2),
                'last_offline_at' => $cam['status'] == CameraStatus::Offline ? Carbon::now()->subHours(5) : null,
                'public_token' => $cam['public_token'],
                'notes' => 'Instalasi lancar menggunakan kabel Cat6.',
                'is_active' => true,
            ]);
        }

        // 5. Create 15 Inventory Items
        $inventoryData = [
            ['sku' => 'CAM-HIK-001', 'name' => 'Dome Camera 4MP DS-2CD2143G0-I', 'category' => 'Camera', 'brand' => 'Hikvision', 'model' => 'DS-2CD2143G0-I', 'stock' => 12, 'min_stock' => 3, 'purchase_price' => 1200000, 'selling_price' => 1500000],
            ['sku' => 'CAM-DAH-001', 'name' => 'Bullet Camera 4MP IPC-HFW2431S', 'category' => 'Camera', 'brand' => 'Dahua', 'model' => 'DH-IPC-HFW2431S', 'stock' => 8, 'min_stock' => 2, 'purchase_price' => 1100000, 'selling_price' => 1400000],
            ['sku' => 'CAM-UNI-001', 'name' => 'Dome Camera 2MP IPC2122LR3', 'category' => 'Camera', 'brand' => 'Uniview', 'model' => 'IPC2122LR3', 'stock' => 5, 'min_stock' => 2, 'purchase_price' => 850000, 'selling_price' => 1050000],
            ['sku' => 'NVR-HIK-001', 'name' => 'NVR 16 Channel DS-7616NI-K2', 'category' => 'NVR', 'brand' => 'Hikvision', 'model' => 'DS-7616NI-K2', 'stock' => 4, 'min_stock' => 1, 'purchase_price' => 2500000, 'selling_price' => 3200000],
            ['sku' => 'NVR-DAH-001', 'name' => 'NVR 8 Channel NVR2108-I', 'category' => 'NVR', 'brand' => 'Dahua', 'model' => 'NVR2108-I', 'stock' => 2, 'min_stock' => 1, 'purchase_price' => 1500000, 'selling_price' => 1950000],
            ['sku' => 'HDD-WD-001', 'name' => 'Harddisk Purple 4TB', 'category' => 'Storage', 'brand' => 'Western Digital', 'model' => 'WD40PURZ', 'stock' => 15, 'min_stock' => 4, 'purchase_price' => 1400000, 'selling_price' => 1750000],
            ['sku' => 'HDD-SE-001', 'name' => 'Harddisk Skyhawk 2TB', 'category' => 'Storage', 'brand' => 'Seagate', 'model' => 'ST2000VX008', 'stock' => 1, 'min_stock' => 3, 'purchase_price' => 900000, 'selling_price' => 1150000], // Low stock!
            ['sku' => 'CBL-BEL-001', 'name' => 'Kabel UTP Cat6 305m', 'category' => 'Cable', 'brand' => 'Belden', 'model' => '7814A', 'stock' => 6, 'min_stock' => 2, 'purchase_price' => 1800000, 'selling_price' => 2200000],
            ['sku' => 'SWT-TP-001', 'name' => 'Switch PoE 8 Port SF1008P', 'category' => 'Switch', 'brand' => 'TP-Link', 'model' => 'TL-SF1008P', 'stock' => 10, 'min_stock' => 2, 'purchase_price' => 750000, 'selling_price' => 950000],
            ['sku' => 'SWT-TP-002', 'name' => 'Switch PoE 16 Port SG1016PE', 'category' => 'Switch', 'brand' => 'TP-Link', 'model' => 'TL-SG1016PE', 'stock' => 0, 'min_stock' => 1, 'purchase_price' => 1800000, 'selling_price' => 2300000], // Out of stock!
            ['sku' => 'BNC-CON-001', 'name' => 'Konektor RJ45 Cat6 (Isi 50)', 'category' => 'Connector', 'brand' => 'AMP', 'model' => 'RJ45-C6', 'stock' => 40, 'min_stock' => 5, 'purchase_price' => 150000, 'selling_price' => 200000],
            ['sku' => 'POW-SUP-001', 'name' => 'Power Supply Jaring 12V 20A', 'category' => 'Power', 'brand' => 'Spc', 'model' => 'PS-12V20A', 'stock' => 8, 'min_stock' => 2, 'purchase_price' => 250000, 'selling_price' => 350000],
            ['sku' => 'BRK-CAM-001', 'name' => 'Bracket Kamera CCTV Dome', 'category' => 'Accessory', 'brand' => 'Generic', 'model' => 'BR-DOME', 'stock' => 25, 'min_stock' => 5, 'purchase_price' => 45000, 'selling_price' => 65000],
            ['sku' => 'BRK-CAM-002', 'name' => 'Bracket Kamera CCTV Bullet', 'category' => 'Accessory', 'brand' => 'Generic', 'model' => 'BR-BULLET', 'stock' => 20, 'min_stock' => 5, 'purchase_price' => 50000, 'selling_price' => 75000],
            ['sku' => 'PIP-PVC-001', 'name' => 'Pipa PVC Protector 20mm (Isi 10)', 'category' => 'Cable Protector', 'brand' => 'Clipsal', 'model' => 'CL-20MM', 'stock' => 30, 'min_stock' => 10, 'purchase_price' => 180000, 'selling_price' => 240000],
        ];

        $inventories = [];
        foreach ($inventoryData as $index => $inv) {
            $loc = $locations[$index % count($locations)];
            $inventories[] = Inventory::create(array_merge($inv, [
                'serial_number' => 'SN-' . strtoupper(Str::random(10)),
                'unit' => 'Pcs',
                'condition' => InventoryCondition::New,
                'location_id' => $loc->id,
                'photo' => null,
            ]));
        }

        // 6. Create 3 Quotations (different statuses)
        // Quotation 1: Accepted
        $q1_sub = floatval($inventories[0]->selling_price * 4) + floatval($inventories[5]->selling_price * 1) + floatval($inventories[7]->selling_price * 2);
        $q1_tax = $q1_sub * 0.12;
        $q1_tot = $q1_sub + $q1_tax;
        $quo1 = Quotation::create([
            'number' => 'QUO-2026-001',
            'client_id' => $clients[0]->id,
            'status' => QuotationStatus::Accepted,
            'valid_until' => Carbon::now()->addDays(30),
            'notes' => 'Penawaran untuk CCTV PT. Sinar Mas Utama - Site 1.',
            'subtotal' => $q1_sub,
            'discount_amount' => 0,
            'tax_percent' => 12,
            'tax_amount' => $q1_tax,
            'total' => $q1_tot,
            'created_by' => $adminUser->id,
        ]);
        QuotationItem::create([
            'quotation_id' => $quo1->id, 'inventory_id' => $inventories[0]->id,
            'description' => $inventories[0]->name, 'qty' => 4, 'unit_price' => $inventories[0]->selling_price, 'subtotal' => $inventories[0]->selling_price * 4
        ]);
        QuotationItem::create([
            'quotation_id' => $quo1->id, 'inventory_id' => $inventories[5]->id,
            'description' => $inventories[5]->name, 'qty' => 1, 'unit_price' => $inventories[5]->selling_price, 'subtotal' => $inventories[5]->selling_price * 1
        ]);
        QuotationItem::create([
            'quotation_id' => $quo1->id, 'inventory_id' => $inventories[7]->id,
            'description' => $inventories[7]->name, 'qty' => 2, 'unit_price' => $inventories[7]->selling_price, 'subtotal' => $inventories[7]->selling_price * 2
        ]);

        // Quotation 2: Sent
        $q2_sub = floatval($inventories[1]->selling_price * 2) + floatval($inventories[8]->selling_price * 1);
        $q2_tax = $q2_sub * 0.12;
        $q2_tot = $q2_sub + $q2_tax;
        $quo2 = Quotation::create([
            'number' => 'QUO-2026-002',
            'client_id' => $clients[1]->id,
            'status' => QuotationStatus::Sent,
            'valid_until' => Carbon::now()->addDays(15),
            'notes' => 'Penawaran instalasi tambahan parkir CV. Jaya Sentosa.',
            'subtotal' => $q2_sub,
            'discount_amount' => 0,
            'tax_percent' => 12,
            'tax_amount' => $q2_tax,
            'total' => $q2_tot,
            'created_by' => $adminUser->id,
        ]);
        QuotationItem::create([
            'quotation_id' => $quo2->id, 'inventory_id' => $inventories[1]->id,
            'description' => $inventories[1]->name, 'qty' => 2, 'unit_price' => $inventories[1]->selling_price, 'subtotal' => $inventories[1]->selling_price * 2
        ]);
        QuotationItem::create([
            'quotation_id' => $quo2->id, 'inventory_id' => $inventories[8]->id,
            'description' => $inventories[8]->name, 'qty' => 1, 'unit_price' => $inventories[8]->selling_price, 'subtotal' => $inventories[8]->selling_price * 1
        ]);

        // Quotation 3: Expired
        $q3_sub = floatval($inventories[2]->selling_price * 8);
        $q3_tax = $q3_sub * 0.12;
        $q3_tot = $q3_sub + $q3_tax;
        $quo3 = Quotation::create([
            'number' => 'QUO-2026-003',
            'client_id' => $clients[2]->id,
            'status' => QuotationStatus::Expired,
            'valid_until' => Carbon::now()->subDays(5),
            'notes' => 'Penawaran kadaluarsa PT. Global Technology.',
            'subtotal' => $q3_sub,
            'discount_amount' => 0,
            'tax_percent' => 12,
            'tax_amount' => $q3_tax,
            'total' => $q3_tot,
            'created_by' => $adminUser->id,
        ]);
        QuotationItem::create([
            'quotation_id' => $quo3->id, 'inventory_id' => $inventories[2]->id,
            'description' => $inventories[2]->name, 'qty' => 8, 'unit_price' => $inventories[2]->selling_price, 'subtotal' => $inventories[2]->selling_price * 8
        ]);

        // 7. Create 3 Invoices (different statuses)
        // Invoice 1: Paid (converted from Quotation 1)
        $inv1 = Invoice::create([
            'number' => 'INV-2026-001',
            'quotation_id' => $quo1->id,
            'client_id' => $clients[0]->id,
            'status' => InvoiceStatus::Paid,
            'issue_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->addDays(10),
            'paid_at' => Carbon::now()->subDays(15),
            'payment_method' => 'Transfer Bank',
            'payment_proof' => 'payment_proofs/demo_proof1.jpg',
            'notes' => 'Invoice lunas PT. Sinar Mas Utama.',
            'subtotal' => $q1_sub,
            'discount_amount' => 0,
            'tax_percent' => 12,
            'tax_amount' => $q1_tax,
            'total' => $q1_tot,
            'created_by' => $adminUser->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv1->id, 'inventory_id' => $inventories[0]->id,
            'description' => $inventories[0]->name, 'qty' => 4, 'unit_price' => $inventories[0]->selling_price, 'subtotal' => $inventories[0]->selling_price * 4
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv1->id, 'inventory_id' => $inventories[5]->id,
            'description' => $inventories[5]->name, 'qty' => 1, 'unit_price' => $inventories[5]->selling_price, 'subtotal' => $inventories[5]->selling_price * 1
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv1->id, 'inventory_id' => $inventories[7]->id,
            'description' => $inventories[7]->name, 'qty' => 2, 'unit_price' => $inventories[7]->selling_price, 'subtotal' => $inventories[7]->selling_price * 2
        ]);

        // Invoice 2: Sent
        $inv2_sub = floatval($inventories[3]->selling_price * 1) + floatval($inventories[10]->selling_price * 2);
        $inv2_tax = $inv2_sub * 0.12;
        $inv2_tot = $inv2_sub + $inv2_tax;
        $inv2 = Invoice::create([
            'number' => 'INV-2026-002',
            'quotation_id' => null,
            'client_id' => $clients[1]->id,
            'status' => InvoiceStatus::Sent,
            'issue_date' => Carbon::now()->subDays(5),
            'due_date' => Carbon::now()->addDays(25),
            'paid_at' => null,
            'payment_method' => null,
            'payment_proof' => null,
            'notes' => 'Invoice dikirim ke CV. Jaya Sentosa.',
            'subtotal' => $inv2_sub,
            'discount_amount' => 0,
            'tax_percent' => 12,
            'tax_amount' => $inv2_tax,
            'total' => $inv2_tot,
            'created_by' => $adminUser->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv2->id, 'inventory_id' => $inventories[3]->id,
            'description' => $inventories[3]->name, 'qty' => 1, 'unit_price' => $inventories[3]->selling_price, 'subtotal' => $inventories[3]->selling_price * 1
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv2->id, 'inventory_id' => $inventories[10]->id,
            'description' => $inventories[10]->name, 'qty' => 2, 'unit_price' => $inventories[10]->selling_price, 'subtotal' => $inventories[10]->selling_price * 2
        ]);

        // Invoice 3: Overdue
        $inv3_sub = floatval($inventories[4]->selling_price * 2);
        $inv3_tax = $inv3_sub * 0.12;
        $inv3_tot = $inv3_sub + $inv3_tax;
        $inv3 = Invoice::create([
            'number' => 'INV-2026-003',
            'quotation_id' => null,
            'client_id' => $clients[2]->id,
            'status' => InvoiceStatus::Overdue,
            'issue_date' => Carbon::now()->subDays(40),
            'due_date' => Carbon::now()->subDays(10),
            'paid_at' => null,
            'payment_method' => null,
            'payment_proof' => null,
            'notes' => 'Invoice jatuh tempo PT. Global Technology.',
            'subtotal' => $inv3_sub,
            'discount_amount' => 0,
            'tax_percent' => 12,
            'tax_amount' => $inv3_tax,
            'total' => $inv3_tot,
            'created_by' => $adminUser->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv3->id, 'inventory_id' => $inventories[4]->id,
            'description' => $inventories[4]->name, 'qty' => 2, 'unit_price' => $inventories[4]->selling_price, 'subtotal' => $inventories[4]->selling_price * 2
        ]);

        // 8. Create 50 Monitoring Log Entries
        $logTypes = ['online', 'offline', 'motion', 'error', 'maintenance'];
        $logDescriptions = [
            'online' => 'Kamera berhasil terhubung ke server media go2rtc.',
            'offline' => 'Kamera kehilangan koneksi. Ping REST API go2rtc gagal.',
            'motion' => 'Terdeteksi gerakan pada area pengawasan kamera.',
            'error' => 'Kamera melaporkan error transmisi RTSP: Connection timed out.',
            'maintenance' => 'Kamera diubah ke status pemeliharaan berkala.',
        ];

        for ($i = 0; $i < 50; $i++) {
            $camera = $cameras[$i % count($cameras)];
            $event = $logTypes[$i % count($logTypes)];
            
            // Adjust event types based on camera status to make it realistic
            if ($camera->status == CameraStatus::Online && $i % 5 == 0) {
                $event = 'online';
            } elseif ($camera->status == CameraStatus::Offline && $i % 5 == 0) {
                $event = 'offline';
            }

            MonitoringLog::create([
                'camera_id' => $camera->id,
                'event_type' => $event,
                'description' => $logDescriptions[$event] ?? 'Event kamera tercatat.',
                'metadata' => [
                    'ip' => $camera->ip_address,
                    'ping_latency_ms' => $event == 'online' ? rand(10, 50) : null,
                    'error_code' => $event == 'error' ? 'RTSP_TIMEOUT_504' : null,
                    'triggered_by' => $event == 'motion' ? 'pixel_change_threshold' : 'system_health_check',
                ],
                'recorded_at' => Carbon::now()->subHours(50 - $i),
            ]);
        }

        // 9. Seed default company settings
        $defaultSettings = [
            'company_name' => 'CV. ROZITECH MULTIMEDIA INDONESIA',
            'company_slogan' => 'IT CONSULTANT | NETWORKING | IT SECURITY',
            'company_address' => 'Jl. Desa Leran RT 01 RW 01, Manyar, Gresik',
            'company_phone' => '(0821) 8782 7382, (0856) 0411 8932',
            'company_email' => 'rozitech.gsk@gmail.com',
            'company_website' => 'rozitech.co.id',
            'signature_name' => 'Fachrur Rozi, S.Kom',
            'signature_title' => 'Direktur Utama',
            'signature_stamp_enable' => '1',
            'signature_path_d' => 'M10,45 Q30,10 45,35 T80,25 T105,40 M45,35 Q60,5 75,55',
        ];

        foreach ($defaultSettings as $key => $val) {
            \App\Models\Setting::set($key, $val, 'company');
        }
    }
}
