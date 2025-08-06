<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Support\Collection;

class DepartmentService
{
    public function all(): Collection
    {
        return Department::all();
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(Department $department, array $data): bool
    {
        return $department->update($data);
    }

    public function delete(Department $department): bool
    {
        return $department->delete();
    }
}
