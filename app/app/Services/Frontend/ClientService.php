<?php

namespace App\Services\Frontend;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
}