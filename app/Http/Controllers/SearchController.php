<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
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

    public function project(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        if ($user->role_id == 1) {
            $data = DB::table('projectview')->where('project_name', 'like', '%' . $request->input('search_data') . '%')->paginate(9);

            return response()->json(['data' => $data], 200);
        } elseif ($user->role_id == 2) {
            $data = DB::table('projectview')->where('project_name', 'like', '%' . $request->input('search_data') . '%')->where('manager_id', $user_id)->paginate(9);

            return response()->json(['data' => $data], 200);
        } else {
            $data_raw = DB::table('members')->where('user_id', $user_id)->get();

            $project_id = [];

            foreach ($data_raw as $value) {
                array_push($project_id, $value->project_id);
            }

            $data = DB::table('projectview')->whereIn('id', $project_id)->where('project_name', 'like', '%' . $request->input('search_data') . '%')->paginate(9);

            return response()->json(['data' => $data], 200);
        }

    }

    public function user(Request $request)
    {
        $data = DB::table('userview')->where('full_name', 'like', '%' . $request->input('search_data') . '%')->paginate(9);

        return response()->json(['data' => $data], 200);
    }

    public function backlog(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        if ($user->role_id == 1 || $user->role_id == 2) {
            $data = DB::table('backlogprojectview')->where('project_id', $request->input('id'))->where('title', 'like', '%' . $request->input('search_data') . '%')->paginate(9);

            return response()->json(['data' => $data], 200);
        } else {
            $member = DB::table('members')->where('user_id', $user_id)->where('project_id', $request->input('id'))->first();

            $member_id = $member->id;
            $data_raw = DB::table('assignees')->where('member_id', $member_id)->get();

            $backlog_id = [];

            foreach ($data_raw as $value) {
                array_push($backlog_id, $value->backlog_id);
            }
            $data = DB::table('backlogprojectview')->where('project_id', $request->input('id'))->where('title', 'like', '%' . $request->input('search_data') . '%')->whereIn('id', $backlog_id)->paginate(9);
            return response()->json(['data' => $data], 200);
        }

    }
}
