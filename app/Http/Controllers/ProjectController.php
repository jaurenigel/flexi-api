<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $user_id = $user->id;

        // check projects with due dates

        $today = date('Y-m-d');

        $raw = DB::table('projects')->where('due_date', '<', $today)->get();

        // get id's

        $ids = [];

        foreach ($raw as $id) {
            array_push($ids, $id->id);
        }

        $overdue = DB::table('projects')->whereIn('id', $ids)->where('status_id', '!=', 3)->update([
            'status_id' => 5,
        ]);

        // fetch all projects

        if ($user->role_id == 2) {

            $projects = DB::table('projectview')->where('manager_id', $user_id)->paginate(9);

            return response()->json(['data' => $projects], 200);
        } else if ($user->role_id == 1) {
            $projects = DB::table('projectview')->paginate(9);

            return response()->json(['data' => $projects], 200);
        } else {
            $data_raw = DB::table('members')->where('user_id', $user_id)->get();

            $project_id = [];

            foreach ($data_raw as $value) {
                array_push($project_id, $value->project_id);
            }
            $projects = DB::table('projectview')->whereIn('id', $project_id)->paginate(9);

            return response()->json(['data' => $projects], 200);
        }

    }

    public function show($id)
    {
        try {
            return new ProjectResource(Project::findOrFail($id));
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Oops! Project not found.'], 404);
        }
    }

    public function create(Request $request)
    {
        $manager = Auth::user();
        try {
            if ($manager->role_id == 1 || $manager->role_id == 2) {
                $project = new Project;

                $project->name = $request->input('name');
                $project->description = $request->input('description');
                $project->due_date = $request->input('due_date');
                $project->manager_id = $manager->id;

                $project->save();

                return new ProjectResource($project);
            } else {
                return response()->json(['error' => 'You must be a project manager to create a project.'], 409);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error occured. Please try again'], 409);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $project = Project::findOrFail($id);
            $project->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'due_date' => $request->input('due_date'),
            ]);
            return new ProjectResource($project);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error occured. Please try again'], 409);
        }
    }
}
