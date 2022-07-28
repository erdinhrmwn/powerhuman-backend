<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
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
        $companies = $request->user()->companies();
        $roles = Role::query()->whereIn('company_id', $companies->pluck('id'))->simplePaginate((int) $limit);

        return ResponseFormatter::success($roles, 'Role successfully retrieved.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        try {
            $company = $request->user()->companies()->find($request->company_id);

            if ($company == null) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $role = $company->roles()->create($request->all());

            return ResponseFormatter::success($role, 'Role successfully created.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        if (! $this->isGranted($role)) {
            return ResponseFormatter::error('You dont have access to this resource.', 403);
        }

        return ResponseFormatter::success($role, 'Role successfully retrieved.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRoleRequest  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            if (! $this->isGranted($role)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $role->update($request->all());

            return ResponseFormatter::success($role, 'Role successfully update.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        try {
            if (! $this->isGranted($role)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $role->delete();

            return ResponseFormatter::success(message: 'Role successfully deleted.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    private function isGranted(Role $role)
    {
        return request()->user()->companies()->where('id', $role->company_id)->exists();
    }
}
