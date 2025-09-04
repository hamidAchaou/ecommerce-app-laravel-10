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

    public function findByUserId(int $userId, array $with = ['user', 'city', 'country']): ?Client
    {
        return $this->model->with($with)->where('user_id', $userId)->first();
    }

    public function createOrUpdate(int $userId, array $data): Client
    {
        $client = $this->findByUserId($userId);
        return $client ? tap($client)->update($data) : $this->create(array_merge($data, ['user_id' => $userId]));
    }

    public function getActiveClients(int $days = 30, array $with = ['user'])
    {
        return $this->model->with($with)
            ->whereHas('orders', fn($q) => $q->where('created_at', '>=', now()->subDays($days)))
            ->get();
    }

    public function getByCountry(string $countryCode, array $with = [], int $perPage = 15)
    {
        return $this->model->with($with)
            ->whereHas('country', fn($q) => $q->where('code', $countryCode))
            ->paginate($perPage);
    }

    public function getCountriesWithCities()
    {
        return \App\Models\Country::with('cities')->get();
    }
}