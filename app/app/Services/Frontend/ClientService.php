<?php

namespace App\Services\Frontend;

use App\Models\Client;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class ClientService
{
    /**
     * Get countries with their cities for checkout form
     */
    public function getCountriesWithCities()
    {
        return Country::with('cities')->get();
    }

    /**
     * Get or create client for a user
     * Following Single Responsibility Principle
     */
    public function getOrCreateClient(User $user, array $clientData): Client
    {
        // Try to find existing client for this user
        $client = Client::where('user_id', $user->id)->first();

        if ($client) {
            // Update client information with latest data
            $client->update($this->prepareClientData($clientData));
            Log::info('Client updated', ['client_id' => $client->id, 'user_id' => $user->id]);
        } else {
            // Create new client
            $client = Client::create(array_merge(
                ['user_id' => $user->id],
                $this->prepareClientData($clientData)
            ));
            Log::info('Client created', ['client_id' => $client->id, 'user_id' => $user->id]);
        }

        return $client;
    }

    /**
     * Prepare client data for database insertion
     * Following clean data handling
     */
    private function prepareClientData(array $data): array
    {
        return [
            'name' => $data['name'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'country_id' => $data['country_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    /**
     * Get client by user ID
     */
    public function getClientByUser(User $user): ?Client
    {
        return Client::where('user_id', $user->id)->first();
    }

    /**
     * Update client information
     */
    public function updateClient(Client $client, array $data): Client
    {
        $client->update($this->prepareClientData($data));
        
        Log::info('Client information updated', [
            'client_id' => $client->id,
            'user_id' => $client->user_id
        ]);

        return $client->fresh();
    }
}