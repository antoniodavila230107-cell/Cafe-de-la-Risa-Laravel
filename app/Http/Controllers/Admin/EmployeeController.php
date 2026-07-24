<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Table;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('tables.zone')->latest()->get();
        $zones = Zone::with('tables')->get();
        $allTables = Table::with('zone')->orderBy('number')->get();

        $stats = [
            'total_count'    => $employees->where('active', true)->count(),
            'waiters_count'  => $employees->where('job_role', 'mesero')->where('active', true)->count(),
            'receptionists'  => $employees->where('job_role', 'recepcionista')->where('active', true)->count(),
            'chefs_count'    => $employees->where('job_role', 'cocinero')->where('active', true)->count(),
            'total_payroll'  => $employees->where('active', true)->sum(fn($e) => $e->monthly_salary),
        ];

        return view('admin.employees.index', compact('employees', 'zones', 'allTables', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'email'            => 'nullable|email|max:150',
            'phone'            => 'nullable|string|max:30',
            'job_role'         => 'required|in:mesero,recepcionista,cocinero',
            'salary'           => 'required|numeric|min:0',
            'salary_period'    => 'required|in:mensual,quincenal,diario',
            'register_station' => 'nullable|string|max:100',
            'table_ids'        => 'nullable|array',
            'table_ids.*'      => 'exists:tables,id',
        ]);

        DB::transaction(function () use ($validated) {
            $employee = Employee::create([
                'name'             => $validated['name'],
                'email'            => $validated['email'] ?? null,
                'phone'            => $validated['phone'] ?? null,
                'job_role'         => $validated['job_role'],
                'salary'           => $validated['salary'],
                'salary_period'    => $validated['salary_period'],
                'register_station' => $validated['job_role'] === 'recepcionista' ? ($validated['register_station'] ?? 'Caja Principal') : null,
                'active'           => true,
            ]);

            if ($validated['job_role'] === 'mesero' && !empty($validated['table_ids'])) {
                $employee->tables()->sync($validated['table_ids']);
            }
        });

        return redirect()->route('admin.employees.index')->with('success', 'Empleado registrado exitosamente y asignado al sistema.');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'email'            => 'nullable|email|max:150',
            'phone'            => 'nullable|string|max:30',
            'job_role'         => 'required|in:mesero,recepcionista,cocinero',
            'salary'           => 'required|numeric|min:0',
            'salary_period'    => 'required|in:mensual,quincenal,diario',
            'register_station' => 'nullable|string|max:100',
            'table_ids'        => 'nullable|array',
            'table_ids.*'      => 'exists:tables,id',
        ]);

        DB::transaction(function () use ($employee, $validated) {
            $employee->update([
                'name'             => $validated['name'],
                'email'            => $validated['email'] ?? null,
                'phone'            => $validated['phone'] ?? null,
                'job_role'         => $validated['job_role'],
                'salary'           => $validated['salary'],
                'salary_period'    => $validated['salary_period'],
                'register_station' => $validated['job_role'] === 'recepcionista' ? ($validated['register_station'] ?? 'Caja Principal') : null,
            ]);

            if ($validated['job_role'] === 'mesero') {
                $employee->tables()->sync($validated['table_ids'] ?? []);
            } else {
                $employee->tables()->detach();
            }
        });

        return redirect()->route('admin.employees.index')->with('success', "Datos del empleado {$employee->name} actualizados.");
    }

    public function toggle(Employee $employee)
    {
        $employee->update(['active' => !$employee->active]);
        $status = $employee->active ? 'activado' : 'desactivado';
        return redirect()->route('admin.employees.index')->with('success', "Empleado {$employee->name} {$status}.");
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Empleado eliminado correctamente.');
    }
}
