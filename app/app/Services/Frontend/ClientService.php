<?php

namespace App\Services\Frontend;

use App\Models\Client;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class ClientService
{
    /**
     * Get the currently authenticated client.
     *
     * @return Client|null
     */
    public function getAuthenticatedClient(): ?Client
    {
        return Auth::check() ? Auth::user() : null;
    }

    public function getAuthenticatedClientAsClient(): ?Client
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        // If User has a related Client model:
        if ($user->client) {
            return $user->client;
        }

        // Or if User IS the Client (same table):
        if ($user instanceof Client) {
            return $user;
        }

        // Or create/find Client based on User:
        return Client::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'email' => $user->email,
                // ... other fields
            ]
        );
    }
    
    public function saveClientInfo(array $data, ?Client $client = null): Client
    {
        try {
            if (!$client) {
                $client = new Client();
                $client->id = (string) Str::uuid(); // best practice for non-incrementing PK
                $client->user_id = auth()->id();
            }

            $client->fill($data);
            $client->save();

            return $client;
        } catch (\Exception $e) {
            Log::error('Error saving client info', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }
    /**
     * Find a client by ID.
     *
     * @param int $id
     * @return Client
     * @throws ModelNotFoundException
     */
    public function findClientById(int $id): Client
    {
        return Client::findOrFail($id);
    }

    /**
     * Create or update client information.
     *
     * @param array $data
     * @param int|null $id
     * @return Client
     */
    public function saveClient(array $data, ?int $id = null): Client
    {
        try {
            if ($id) {
                $client = Client::findOrFail($id);
                $client->update($data);
            } else {
                $client = Client::create($data);
            }

            return $client;
        } catch (\Exception $e) {
            Log::error('Error saving client: ' . $e->getMessage(), [
                'data' => $data,
                'id'   => $id
            ]);

            throw $e;
        }
    }

    /**
     * Delete a client.
     *
     * @param int $id
     * @return bool
     */
    public function deleteClient(int $id): bool
    {
        try {
            $client = Client::findOrFail($id);
            return $client->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting client: ' . $e->getMessage(), ['id' => $id]);
            return false;
        }
    }
    // public function getCountriesWithCities()
    // {
    //     return Country::with('cities:id,name,country_id')
    //                   ->select('id', 'name', 'code')
    //                   ->orderBy('name')
    //                   ->get();
    // }
        /**
     * Get countries with their cities
     */
    public function getCountriesWithCities()
    {
        // Assuming you have Country and City models
        return \App\Models\Country::with('cities')->get();
    }
}