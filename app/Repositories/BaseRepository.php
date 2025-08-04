<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = $this->getModelInstance();
    }

    abstract protected function model(): string;

    protected function getModelInstance(): Model
    {
        return app()->make($this->model());
    }

    public function all(array $with = [])
    {
        return $this->model->with($with)->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Advanced search & filter
     */
    public function search(array $filters = [], array $with = [], array $searchableFields = [], string $orderBy = 'created_at', string $direction = 'desc')
    {
        $query = $this->model->with($with);

        // Search keyword in multiple fields
        if (!empty($filters['search']) && count($searchableFields)) {
            $keyword = $filters['search'];
            $query->where(function (Builder $q) use ($keyword, $searchableFields) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$keyword}%");
                }
            });
        }

        // Apply other filters like status, type, etc.
        foreach ($filters as $field => $value) {
            if ($field !== 'search' && $value !== null) {
                $query->where($field, $value);
            }
        }

        return $query->orderBy($orderBy, $direction)->get();
    }
}