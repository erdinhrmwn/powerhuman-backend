<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Http\Requests\UpdateResponsibilityRequest;
use App\Models\Responsibility;
use App\Models\Role;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
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
        $responsibilities = Responsibility::query()->whereIn('role_id', $roles)->simplePaginate((int) $limit);

        return ResponseFormatter::success($responsibilities, 'Responsibility successfully retrieved.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateResponsibilityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateResponsibilityRequest $request)
    {
        try {
            $role = Role::query()->find($request->role_id);
            $company = $request->user()->companies()->find($role->company_id);
            if ($company == null) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $responsibility = $role->responsibilities()->create($request->all());

            return ResponseFormatter::success($responsibility, 'Responsibility successfully created.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Responsibility  $responsibility
     * @return \Illuminate\Http\Response
     */
    public function show(Responsibility $responsibility)
    {
        if (! $this->isGranted($responsibility)) {
            return ResponseFormatter::error('You dont have access to this resource.', 403);
        }

        return ResponseFormatter::success($responsibility, 'Responsibility successfully retrieved.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResponsibilityRequest  $request
     * @param  \App\Models\Responsibility  $responsibility
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResponsibilityRequest $request, Responsibility $responsibility)
    {
        try {
            if (! $this->isGranted($responsibility)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $responsibility->update($request->all());

            return ResponseFormatter::success($responsibility, 'Responsibility successfully update.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Responsibility  $responsibility
     * @return \Illuminate\Http\Response
     */
    public function destroy(Responsibility $responsibility)
    {
        try {
            if (! $this->isGranted($responsibility)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $responsibility->delete();

            return ResponseFormatter::success(message: 'Responsibility successfully deleted.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    private function isGranted(Responsibility $responsibility)
    {
        $companies = request()->user()->companies()->pluck('id');
        $granted = Role::query()->whereIn('company_id', $companies)->where('id', $responsibility->role_id)->exists();

        return $granted;
    }
}
