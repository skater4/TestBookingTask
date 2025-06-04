<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template T of Model
 */
abstract class BaseRepository
{
    /**
     * @var T
     */
    protected Model $model;

    public function __construct()
    {
        /** @var T $modelInstance */
        $modelInstance = app($this->getModelClass());
        $this->model = $modelInstance;
    }

    /**
     * @return Collection<int, T>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @return T|null
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return T
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param  T  $model
     * @param  array<string, mixed>  $data
     */
    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    /**
     * @param  T  $model
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    abstract protected function getModelClass(): string;
}
