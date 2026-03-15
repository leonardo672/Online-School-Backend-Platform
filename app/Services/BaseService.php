<?php
// app/Services/BaseService.php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function delete(int $id): bool
    {
        $model = $this->find($id);
        return $model->delete();
    }

    public function count(): int
    {
        return $this->model->count();
    }
}