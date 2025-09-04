<?php

namespace App\Services\Frontend;

use App\Models\Client;
use App\Models\User;
use App\Repositories\Frontend\ClientRepository;
use Illuminate\Support\Facades\Log;

class ClientService
{
    public function __construct(protected ClientRepository $clientRepo) {}

    /**
     * Get countries with their cities for checkout form
     */
    public function getCountriesWithCities()
    {
        return $this->clientRepo->getCountriesWithCities();
    }

    /**
     * Get or create client for a user
     */
    public function getOrCreateClient(User $user, array $data): Client
    {
        $client = $this->clientRepo->createOrUpdate($user->id, $this->prepareClientData($data));

        Log::info($client->wasRecentlyCreated ? 'Client created' : 'Client updated', [
            'client_id' => $client->id,
            'user_id' => $user->id,
        ]);

        return $client;
    }

    /**
     * Update client information
     */
    public function updateClient(Client $client, array $data): Client
    {
        $client->update($this->prepareClientData($data));

        Log::info('Client information updated', [
            'client_id' => $client->id,
            'user_id' => $client->user_id,
        ]);

        return $client->fresh();
    }

    /**
     * Prepare client data safely
     */
    private function prepareClientData(array $data): array
    {
        return [
            'name'       => $data['name'] ?? '',
            'phone'      => $data['phone'] ?? '',
            'address'    => $data['address'] ?? '',
            'country_id' => $data['country_id'] ?? null,
            'city_id'    => $data['city_id'] ?? null,
            'notes'      => $data['notes'] ?? null,
        ];
    }

    /**
     * Get client by user
     */
    public function getClientByUser(User $user): ?Client
    {
        return $this->clientRepo->findByUserId($user->id);
    }
}