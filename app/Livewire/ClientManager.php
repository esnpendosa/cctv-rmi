<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Location;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class ClientManager
 * 
 * Handles Client and Client Locations CRUD operations.
 * 
 * @package App\Livewire
 */
class ClientManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    // Client Form States
    public bool $isClientFormOpen = false;
    public ?int $clientId = null;
    public string $clientName = '';
    public string $clientCompany = '';
    public string $clientEmail = '';
    public string $clientPhone = '';
    public string $clientAddress = '';

    // Location Form States
    public bool $isLocationFormOpen = false;
    public ?int $locationId = null;
    public ?int $locationClientId = null;
    public string $locationName = '';
    public string $locationAddress = '';
    public ?float $locationLatitude = null;
    public ?float $locationLongitude = null;

    // Sub-view: Active client to show locations
    public ?int $activeClientIdForLocations = null;

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open form to create new client.
     */
    public function createClient(): void
    {
        $this->resetClientForm();
        $this->isClientFormOpen = true;
    }

    /**
     * Open form to edit existing client.
     * 
     * @param int $id
     * @return void
     */
    public function editClient(int $id): void
    {
        $this->resetClientForm();
        $client = Client::findOrFail($id);
        
        $this->clientId = $client->id;
        $this->clientName = $client->name;
        $this->clientCompany = $client->company;
        $this->clientEmail = $client->email;
        $this->clientPhone = $client->phone;
        $this->clientAddress = $client->address;

        $this->isClientFormOpen = true;
    }

    /**
     * Save client data.
     */
    public function saveClient(): void
    {
        $this->validate([
            'clientName' => 'required|string|max:100',
            'clientCompany' => 'required|string|max:100',
            'clientEmail' => 'required|email|max:100',
            'clientPhone' => 'required|string|max:20',
            'clientAddress' => 'required|string',
        ]);

        $data = [
            'name' => $this->clientName,
            'company' => $this->clientCompany,
            'email' => $this->clientEmail,
            'phone' => $this->clientPhone,
            'address' => $this->clientAddress,
        ];

        if ($this->clientId) {
            Client::findOrFail($this->clientId)->update($data);
            session()->flash('success', 'Data klien berhasil diperbarui.');
        } else {
            Client::create($data);
            session()->flash('success', 'Klien baru berhasil ditambahkan.');
        }

        $this->closeClientForm();
    }

    /**
     * Delete client.
     * 
     * @param int $id
     * @return void
     */
    public function deleteClient(int $id): void
    {
        $client = Client::findOrFail($id);
        if ($client->locations()->count() > 0) {
            session()->flash('error', 'Tidak dapat menghapus klien ini karena masih memiliki lokasi terkait.');
            return;
        }
        $client->delete();
        session()->flash('success', 'Klien berhasil dihapus.');
    }

    /**
     * View locations for a client.
     * 
     * @param int $clientId
     * @return void
     */
    public function viewLocations(int $clientId): void
    {
        $this->activeClientIdForLocations = $this->activeClientIdForLocations === $clientId ? null : $clientId;
    }

    /**
     * Open form to create a location.
     * 
     * @param int $clientId
     * @return void
     */
    public function createLocation(int $clientId): void
    {
        $this->resetLocationForm();
        $this->locationClientId = $clientId;
        $this->isLocationFormOpen = true;
    }

    /**
     * Open form to edit a location.
     * 
     * @param int $id
     * @return void
     */
    public function editLocation(int $id): void
    {
        $this->resetLocationForm();
        $loc = Location::findOrFail($id);
        
        $this->locationId = $loc->id;
        $this->locationClientId = $loc->client_id;
        $this->locationName = $loc->name;
        $this->locationAddress = $loc->address;
        $this->locationLatitude = $loc->latitude;
        $this->locationLongitude = $loc->longitude;

        $this->isLocationFormOpen = true;
    }

    /**
     * Save location data.
     */
    public function saveLocation(): void
    {
        $this->validate([
            'locationName' => 'required|string|max:100',
            'locationAddress' => 'required|string',
            'locationLatitude' => 'nullable|numeric|between:-90,90',
            'locationLongitude' => 'nullable|numeric|between:-180,180',
        ]);

        $data = [
            'client_id' => $this->locationClientId,
            'name' => $this->locationName,
            'address' => $this->locationAddress,
            'latitude' => $this->locationLatitude,
            'longitude' => $this->locationLongitude,
        ];

        if ($this->locationId) {
            Location::findOrFail($this->locationId)->update($data);
            session()->flash('success', 'Data lokasi berhasil diperbarui.');
        } else {
            Location::create($data);
            session()->flash('success', 'Lokasi baru berhasil ditambahkan.');
        }

        $this->closeLocationForm();
    }

    /**
     * Delete location.
     * 
     * @param int $id
     * @return void
     */
    public function deleteLocation(int $id): void
    {
        $loc = Location::findOrFail($id);
        if ($loc->cameras()->count() > 0 || $loc->inventories()->count() > 0) {
            session()->flash('error', 'Tidak dapat menghapus lokasi ini karena masih memiliki kamera atau inventaris terkait.');
            return;
        }
        $loc->delete();
        session()->flash('success', 'Lokasi berhasil dihapus.');
    }

    /**
     * Close client modal.
     */
    public function closeClientForm(): void
    {
        $this->isClientFormOpen = false;
        $this->resetClientForm();
    }

    /**
     * Close location modal.
     */
    public function closeLocationForm(): void
    {
        $this->isLocationFormOpen = false;
        $this->resetLocationForm();
    }

    /**
     * Reset client fields.
     */
    protected function resetClientForm(): void
    {
        $this->clientId = null;
        $this->clientName = '';
        $this->clientCompany = '';
        $this->clientEmail = '';
        $this->clientPhone = '';
        $this->clientAddress = '';
        $this->resetErrorBag();
    }

    /**
     * Reset location fields.
     */
    protected function resetLocationForm(): void
    {
        $this->locationId = null;
        $this->locationClientId = null;
        $this->locationName = '';
        $this->locationAddress = '';
        $this->locationLatitude = null;
        $this->locationLongitude = null;
        $this->resetErrorBag();
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        $query = Client::withCount('locations');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('company', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $clients = $query->paginate(10);

        // Fetch location list for active client
        $activeClientLocations = $this->activeClientIdForLocations 
            ? Location::where('client_id', $this->activeClientIdForLocations)->get() 
            : collect();

        return view('livewire.client-manager', [
            'clients' => $clients,
            'activeClientLocations' => $activeClientLocations,
            'activeClient' => $this->activeClientIdForLocations ? Client::find($this->activeClientIdForLocations) : null,
        ])->layout('layouts.app');
    }
}
