<?php

namespace App\Repositories\Frontend;

use App\Models\Client;
use App\Repositories\BaseRepository;

class ClientRepository extends BaseRepository
{
    protected function model(): string
    {
        return Client::class;
    }

    /**
     * Find client by user ID
     */
    public function findByUserId(int $userId, array $with = ['user', 'city', 'country'])
    {
        return $this->model
            ->with($with)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Create or update client
     */
    public function createOrUpdate(int $userId, array $data): Client
    {
        $client = $this->findByUserId($userId);
        
        if ($client) {
            $client->update($data);
            return $client;
        }

        return $this->create(array_merge($data, ['user_id' => $userId]));
    }

    /**
     * Get clients with recent orders
     */
    public function getActiveClients(int $days = 30, array $with = ['user'])
    {
        return $this->model
            ->with($with)
            ->whereHas('orders', function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            })
            ->get();
    }

    /**
     * Get clients by country
     */
    public function getByCountry(string $countryCode, array $with = [], int $perPage = 15)
    {
        return $this->model
            ->with($with)
            ->whereHas('country', function ($query) use ($countryCode) {
                $query->where('code', $countryCode);
            })
            ->paginate($perPage);
    }
}