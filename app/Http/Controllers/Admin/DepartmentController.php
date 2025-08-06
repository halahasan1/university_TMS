<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\DepartmentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\DepartmentStoreRequest;
use App\Http\Requests\Department\DepartmentUpdateRequest;

class DepartmentController extends Controller
{
    protected DepartmentService $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Department::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        return view('admin.departments.index', [
            'departments' => $query->latest()->get()
        ]);
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(DepartmentStoreRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(DepartmentUpdateRequest $request, Department $department)
    {
        $this->service->update($department, $request->validated());
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $this->service->delete($department);
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
