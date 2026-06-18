<?php

namespace App\Livewire;

use App\Models\Camera;
use Livewire\Component;

/**
 * Class PublicCameraViewer
 * 
 * Handles public viewing of a camera stream via sharing token.
 * 
 * @package App\Livewire
 */
class PublicCameraViewer extends Component
{
    /**
     * @var Camera|null
     */
    public ?Camera $camera = null;

    /**
     * @var string|null
     */
    public ?string $error = null;

    /**
     * Mount component data.
     * 
     * @param string $token
     * @return void
     */
    public function mount(string $token): void
    {
        $camera = Camera::with('location')->where('public_token', $token)->first();

        if (!$camera) {
            $this->error = 'Tautan tidak valid atau kamera tidak ditemukan.';
            return;
        }

        if ($camera->access !== \App\Enums\CameraAccess::Public) {
            $this->error = 'Akses publik untuk kamera ini telah dicabut.';
            return;
        }

        $this->camera = $camera;
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.public-camera-viewer')
            ->layout('layouts.guest');
    }
}
