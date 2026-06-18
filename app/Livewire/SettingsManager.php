<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

use Livewire\WithFileUploads;

/**
 * Class SettingsManager
 * 
 * Handles CCTV System & Financial Letterhead and Configuration.
 * 
 * @package App\Livewire
 */
class SettingsManager extends Component
{
    use WithFileUploads;

    // Company / Letterhead Settings
    public string $companyName = '';
    public string $companySlogan = '';
    public string $companyAddress = '';
    public string $companyPhone = '';
    public string $companyEmail = '';
    public string $companyWebsite = '';

    // Upload files
    public $logoFile;
    public $stampFile;
    public $signatureFile;
    public $stampSignatureFile;

    // Path storage settings
    public ?string $logoPath = null;
    public ?string $stampPath = null;
    public ?string $signaturePath = null;
    public ?string $stampSignaturePath = null;

    // Signature Settings
    public string $signatureName = '';
    public string $signatureTitle = '';
    public bool $signatureStampEnable = true;
    public string $signaturePathD = '';

    // System Configs
    public int $defaultTaxRate = 12;
    public string $quotationPrefix = 'QUO';
    public string $invoicePrefix = 'INV';

    /**
     * Initialize form with current settings or config fallbacks.
     */
    public function mount(): void
    {
        $this->companyName = Setting::get('company_name', 'CV. ROZITECH MULTIMEDIA INDONESIA');
        $this->companySlogan = Setting::get('company_slogan', 'IT CONSULTANT | NETWORKING | IT SECURITY');
        $this->companyAddress = Setting::get('company_address', 'Jl. Desa Leran RT 01 RW 01, Manyar, Gresik');
        $this->companyPhone = Setting::get('company_phone', '(0821) 8782 7382, (0856) 0411 8932');
        $this->companyEmail = Setting::get('company_email', 'rozitech.gsk@gmail.com');
        $this->companyWebsite = Setting::get('company_website', 'rozitech.co.id');

        $this->logoPath = Setting::get('company_logo_path');
        $this->stampPath = Setting::get('company_stamp_path');
        $this->signaturePath = Setting::get('company_signature_path');
        $this->stampSignaturePath = Setting::get('company_stamp_signature_path');

        $this->signatureName = Setting::get('signature_name', 'Fachrur Rozi, S.Kom');
        $this->signatureTitle = Setting::get('signature_title', 'Direktur Utama');
        $this->signatureStampEnable = (bool) Setting::get('signature_stamp_enable', '1');
        $this->signaturePathD = Setting::get('signature_path_d', 'M10,45 Q30,10 45,35 T80,25 T105,40 M45,35 Q60,5 75,55');

        $this->defaultTaxRate = (int) Setting::get('default_tax_rate', config('cctv.default_tax_rate', 12));
        $this->quotationPrefix = Setting::get('quotation_prefix', config('cctv.quotation_prefix', 'QUO'));
        $this->invoicePrefix = Setting::get('invoice_prefix', config('cctv.invoice_prefix', 'INV'));
    }

    /**
     * Save all setting values.
     */
    public function saveSettings(): void
    {
        $this->validate([
            'companyName' => 'required|string|max:255',
            'companySlogan' => 'nullable|string|max:255',
            'companyAddress' => 'required|string',
            'companyPhone' => 'required|string|max:100',
            'companyEmail' => 'required|email|max:100',
            'companyWebsite' => 'required|string|max:100',
            'signatureName' => 'required|string|max:100',
            'signatureTitle' => 'required|string|max:100',
            'signaturePathD' => 'nullable|string',
            'defaultTaxRate' => 'required|integer|min:0|max:100',
            'quotationPrefix' => 'required|string|max:10',
            'invoicePrefix' => 'required|string|max:10',
            'logoFile' => 'nullable|image|max:2048',
            'stampFile' => 'nullable|image|max:2048',
            'signatureFile' => 'nullable|image|max:2048',
            'stampSignatureFile' => 'nullable|image|max:2048',
        ]);

        if ($this->logoFile) {
            $this->logoPath = $this->logoFile->store('logos', 'public');
            Setting::set('company_logo_path', $this->logoPath, 'company');
            $this->logoFile = null; // Clear file input
        }

        if ($this->stampFile) {
            $this->stampPath = $this->stampFile->store('stamps', 'public');
            Setting::set('company_stamp_path', $this->stampPath, 'signature');
            $this->stampFile = null;
        }

        if ($this->signatureFile) {
            $this->signaturePath = $this->signatureFile->store('signatures', 'public');
            Setting::set('company_signature_path', $this->signaturePath, 'signature');
            $this->signatureFile = null;
        }

        if ($this->stampSignatureFile) {
            $this->stampSignaturePath = $this->stampSignatureFile->store('stamp_signatures', 'public');
            Setting::set('company_stamp_signature_path', $this->stampSignaturePath, 'signature');
            $this->stampSignatureFile = null;
        }

        Setting::set('company_name', $this->companyName, 'company');
        Setting::set('company_slogan', $this->companySlogan, 'company');
        Setting::set('company_address', $this->companyAddress, 'company');
        Setting::set('company_phone', $this->companyPhone, 'company');
        Setting::set('company_email', $this->companyEmail, 'company');
        Setting::set('company_website', $this->companyWebsite, 'company');

        Setting::set('signature_name', $this->signatureName, 'signature');
        Setting::set('signature_title', $this->signatureTitle, 'signature');
        Setting::set('signature_stamp_enable', $this->signatureStampEnable ? '1' : '0', 'signature');
        Setting::set('signature_path_d', $this->signaturePathD, 'signature');

        Setting::set('default_tax_rate', (string) $this->defaultTaxRate, 'system');
        Setting::set('quotation_prefix', $this->quotationPrefix, 'system');
        Setting::set('invoice_prefix', $this->invoicePrefix, 'system');

        session()->flash('success', 'Pengaturan berhasil diperbarui.');
    }

    /**
     * Render the settings page.
     */
    public function render()
    {
        return view('livewire.settings-manager')
            ->layout('layouts.app');
    }
}
