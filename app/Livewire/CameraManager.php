<?php

namespace App\Livewire;

use App\Enums\CameraAccess;
use App\Enums\CameraStatus;
use App\Models\Camera;
use App\Models\Location;
use App\Models\CameraCategory;
use App\Repositories\Interfaces\CameraRepositoryInterface;
use App\Services\CameraService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class CameraManager
 * 
 * Manages CCTV camera listing, additions, edits, and sharing access.
 * 
 * @package App\Livewire
 */
class CameraManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public string $search = '';
    public string $statusFilter = '';
    public string $locationFilter = '';
    public string $categoryFilter = '';
    public string $viewMode = 'grid';

    // Form states
    public bool $isFormOpen = false;
    public ?int $cameraId = null;
    public string $name = '';
    public string $brand = '';
    public string $model = '';
    public string $ip_address = '';
    public string $rtsp_url = '';
    public ?int $location_id = null;
    public ?int $category_id = null;
    public string $status = 'online';
    public ?float $latitude = null;
    public ?float $longitude = null;

    // Sharing modal
    public bool $isShareOpen = false;
    public ?Camera $sharingCamera = null;
    public string $expires_at = '';

    /**
     * Set up validation rules.
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'ip_address' => 'required|ip',
            'rtsp_url' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'category_id' => 'required|exists:camera_categories,id',
            'status' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Reset pagination when searching.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open form to create a new camera.
     */
    public function create(): void
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    /**
     * Edit an existing camera.
     * 
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->resetForm();
        $camera = Camera::findOrFail($id);
        
        $this->cameraId = $camera->id;
        $this->name = $camera->name;
        $this->brand = $camera->brand;
        $this->model = $camera->model;
        $this->ip_address = $camera->ip_address;
        $this->rtsp_url = $camera->rtsp_url; // Automatically decrypted via casting
        $this->location_id = $camera->location_id;
        $this->category_id = $camera->category_id;
        $this->status = $camera->status->value;
        $this->latitude = $camera->latitude;
        $this->longitude = $camera->longitude;

        $this->isFormOpen = true;
    }

    /**
     * Save camera (create or update).
     * 
     * @param CameraRepositoryInterface $cameraRepository
     * @return void
     */
    public function save(CameraRepositoryInterface $cameraRepository): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'brand' => $this->brand,
            'model' => $this->model,
            'ip_address' => $this->ip_address,
            'rtsp_url' => $this->rtsp_url,
            'location_id' => $this->location_id,
            'category_id' => $this->category_id,
            'status' => CameraStatus::from($this->status),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => true,
        ];

        if ($this->cameraId) {
            $camera = Camera::findOrFail($this->cameraId);
            $camera->update($data);
            session()->flash('success', 'Kamera CCTV berhasil diperbarui.');
        } else {
            Camera::create($data);
            session()->flash('success', 'Kamera CCTV baru berhasil ditambahkan.');
        }

        $this->closeForm();
    }

    /**
     * Delete a camera.
     * 
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $camera = Camera::findOrFail($id);
        $camera->delete();
        session()->flash('success', 'Kamera CCTV berhasil dihapus.');
    }

    /**
     * Open sharing modal for public link generation.
     * 
     * @param int $id
     * @return void
     */
    public function openShare(int $id): void
    {
        $this->sharingCamera = Camera::findOrFail($id);
        $this->expires_at = '';
        $this->isShareOpen = true;
    }

    /**
     * Generate shareable public URL.
     * 
     * @param CameraService $cameraService
     * @return void
     */
    public function generateShareLink(CameraService $cameraService): void
    {
        if ($this->sharingCamera) {
            $cameraService->setPublicAccess($this->sharingCamera, true);
            
            // Reload camera state
            $this->sharingCamera = Camera::find($this->sharingCamera->id);
            session()->flash('success', 'Tautan berbagi publik berhasil diperbarui.');
        }
    }

    /**
     * Revoke public share link access.
     * 
     * @param CameraService $cameraService
     * @return void
     */
    public function revokeShareLink(CameraService $cameraService): void
    {
        if ($this->sharingCamera) {
            $cameraService->revokePublicAccess($this->sharingCamera);
            
            // Reload camera state
            $this->sharingCamera = Camera::find($this->sharingCamera->id);
            $this->expires_at = '';
            session()->flash('success', 'Akses tautan berbagi publik berhasil dicabut.');
        }
    }

    /**
     * Close sharing modal.
     */
    public function closeShare(): void
    {
        $this->isShareOpen = false;
        $this->sharingCamera = null;
    }

    /**
     * Close input form modal.
     */
    public function closeForm(): void
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    /**
     * Reset form fields.
     */
    protected function resetForm(): void
    {
        $this->cameraId = null;
        $this->name = '';
        $this->brand = '';
        $this->model = '';
        $this->ip_address = '';
        $this->rtsp_url = '';
        $this->location_id = null;
        $this->category_id = null;
        $this->status = 'online';
        $this->latitude = null;
        $this->longitude = null;
        $this->resetErrorBag();
    }

    /**
     * Render the component view.
     */
    public function render(CameraRepositoryInterface $cameraRepository)
    {
        $query = Camera::with(['location', 'category']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                  ->orWhere('brand', 'like', '%' . $this->search . '%')
                  ->orWhere('model', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->locationFilter) {
            $query->where('location_id', $this->locationFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        $cameras = $query->paginate(10);
        $locations = Location::all();
        $categories = CameraCategory::all();

        return view('livewire.camera-manager', [
            'cameras' => $cameras,
            'locations' => $locations,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
