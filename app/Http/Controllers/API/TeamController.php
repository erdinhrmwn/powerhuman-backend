<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
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
        $teams = Team::query()->whereIn('company_id', $companies->pluck('id'))->simplePaginate((int) $limit);

        return ResponseFormatter::success($teams, 'Team successfully retrieved.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateTeamRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTeamRequest $request)
    {
        try {
            $company = $request->user()->companies()->find($request->company_id);

            if ($company == null) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
                $request->icon = $path;
            }

            $team = $company->teams()->create($request->all());

            return ResponseFormatter::success($team, 'Team successfully created.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        if (! $this->isGranted($team)) {
            return ResponseFormatter::error('You dont have access to this resource.', 403);
        }

        return ResponseFormatter::success($team, 'Team successfully retrieved.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTeamRequest  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        try {
            if (! $this->isGranted($team)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
                $request->icon = $path;
            }

            $team->update($request->all());

            return ResponseFormatter::success($team, 'Team successfully update.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        try {
            if (! $this->isGranted($team)) {
                throw new \Exception('You dont have access to this resource.', 403);
            }

            $team->delete();

            return ResponseFormatter::success(message: 'Team successfully deleted.');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), $th->getCode());
        }
    }

    private function isGranted(Team $team)
    {
        return request()->user()->companies()->where('id', $team->company_id)->exists();
    }
}
