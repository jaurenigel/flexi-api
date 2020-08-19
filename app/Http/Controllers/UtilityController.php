<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UtilityController extends Controller
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

    // fetch project members not in members table  using project  id ==== utilities/api

    public function index($id)
    {
        $data_raw = DB::table('memberview')->where('project_id', $id)->get();
        $data = [];

        foreach ($data_raw as $value) {
            array_push($data, $value->user_id);
        }

        $user = DB::table('userview')->whereNotIn('id', $data)->get();
        return response()->json($user, 200);
    }

    public function membersById($id)
    {
        $data = DB::table('memberview')->where('project_id', $id)->get();
        return response()->json(['data' => $data], 200);
    }

    // fetch project members not in assigned table  using backlog  id ==== utilities/api

    public function assigneesIndex($id)
    {
        $data_raw = DB::table('assignees')->where('backlog_id', $id)->get();

        $data = [];

        foreach ($data_raw as $value) {
            array_push($data, $value->member_id);
        }

        $project_id = [];

        foreach ($data_raw as $value) {
            array_push($project_id, $value->project_id);
        }

        if (empty($data)) {
            $backlog = DB::table('backlogs')->where('id', $id)->first();
            $user = DB::table('memberview')->where('project_id', $backlog->project_id)->get();

            return response()->json($user, 200);
        } else {
            $user = DB::table('memberview')->whereNotIn('id', $data)->whereIn('project_id', $project_id)->get();
            return response()->json($user, 200);
        }

    }

    // fetch backlog assinged users using backlog id ==== utilities/api

    public function getAssignees($id)
    {
        $data = DB::table('assignees')->where('backlog_id', $id)->get();
        return response()->json(['data' => $data], 200);
    }

    // fetch backlogs using project id ==== utilities/api
    public function backlogs($id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        if ($user->role_id == 1 || $user->role_id == 2) {
            $data = DB::table('backlogprojectview')->where('project_id', $id)->get();
            return response()->json(['data' => $data], 200);
        } else {
            $member = DB::table('members')->where('user_id', $user_id)->where('project_id', $id)->first();
            $member_id = $member->id;

            $data_raw = DB::table('assignees')->where('member_id', $member_id)->get();

            $backlog_id = [];

            foreach ($data_raw as $value) {
                array_push($backlog_id, $value->backlog_id);
            }

            $data = DB::table('backlogprojectview')->whereIn('id', $backlog_id)->where('project_id', $id)->get();
            return response()->json(['data' => $data], 200);
        }
    }

    // group update backlogs (change status only) ==== utilities/api
    public function backlogUpdate(Request $request)
    {
        // pending

        if (!empty($request->input('pending'))) {
            $data = $request->input('pending');
            $backlogs = DB::table('backlogs')->get();

            foreach ($backlogs as $bl) {
                $id = $bl->id;
                foreach ($data as $value) {
                    if ($value['id'] == $id) {
                        DB::table('backlogs')->where('id', $id)->update([
                            'status_id' => 1,
                        ]);
                    }
                }
            }

        }

        // progress

        if (!empty($request->input('progress'))) {
            $data = $request->input('progress');
            $backlogs = DB::table('backlogs')->get();

            foreach ($backlogs as $bl) {
                $id = $bl->id;
                foreach ($data as $value) {
                    if ($value['id'] == $id) {
                        DB::table('backlogs')->where('id', $id)->update([
                            'status_id' => 2,
                        ]);

                        // update project status

                        $b_log = DB::table('backlogs')->where('id', $id)->first();

                        $pro = DB::table('projects')->where('id', $b_log->project_id)->first();

                        if ($pro->status_id == 1) {
                            DB::table('projects')->where('id', $pro->id)->update([
                                'status_id' => 2,
                            ]);
                        }
                    }
                }
            }

        }

        // testing

        if (!empty($request->input('testing'))) {
            $data = $request->input('testing');
            $backlogs = DB::table('backlogs')->get();

            foreach ($backlogs as $bl) {
                $id = $bl->id;
                foreach ($data as $value) {
                    if ($value['id'] == $id) {
                        DB::table('backlogs')->where('id', $id)->update([
                            'status_id' => 4,
                        ]);
                    }
                }
            }

        }

        // completed

        if (!empty($request->input('completed'))) {
            $data = $request->input('completed');
            $backlogs = DB::table('backlogs')->get();

            foreach ($backlogs as $bl) {
                $id = $bl->id;
                foreach ($data as $value) {
                    if ($value['id'] == $id) {
                        DB::table('backlogs')->where('id', $id)->update([
                            'status_id' => 3,
                        ]);
                    }
                }
            }

        }

    }
}
