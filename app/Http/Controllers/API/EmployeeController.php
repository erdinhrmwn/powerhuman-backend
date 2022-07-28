<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $companies = $request->user()->companies()->pluck('id');
        $roles = Role::query()->whereIn('company_id', $companies)->pluck('id');
        $teams = Team::query()->whereIn('company_id', $companies)->pluck('id');
        $employees = Employee::query()->whereIn('team_id', $teams)->orWhereIn('role_id', $roles)->simplePaginate((int) $limit);

        return ResponseFormatter::success($employees, 'Employee successfully retrieved.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateEmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateEmployeeRequest $request)
    {
        try {
            $role = Role::query()->find($request->role_id);
            $team = Team::query()->find($request->team_id);

            $granted = $request->user()->companies()->where('role_id', $role->id)->orWhere('team_id', $team->id)->exists();
            if (! $granted) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $employee = Employee::query()->create($request->all());

            return ResponseFormatter::success($employee, 'Employee successfully created.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        if (! $this->isGranted($employee)) {
            return ResponseFormatter::error('You dont have access to this resource.', 403);
        }

        return ResponseFormatter::success($employee, 'Employee successfully retrieved.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeRequest  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            if (! $this->isGranted($employee)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $employee->update($request->all());

            return ResponseFormatter::success($employee, 'Employee successfully update.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        try {
            if (! $this->isGranted($employee)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $employee->delete();

            return ResponseFormatter::success(message: 'Employee successfully deleted.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    private function isGranted(Employee $employee)
    {
        $companies = request()->user()->companies()->pluck('id');
        $roles = Role::query()->whereIn('company_id', $companies)->pluck('id');
        $teams = Team::query()->whereIn('company_id', $companies)->pluck('id');
        // $granted = Employee::query()->whereIn('team_id', $teams)->orWhereIn('role_id', $roles)->where('id', $employee->id)->exists();

        return $roles->contains($employee->role_id) && $teams->contains($employee->team_id);
    }
}
