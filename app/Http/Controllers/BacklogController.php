<?php

namespace App\Http\Controllers;

use App\Backlog;
use App\Mail\Test;
use App\MailerCustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BacklogResource;



class BacklogController extends Controller
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
        return BacklogResource::collection(Backlog::with('comments', 'type', 'status', 'priority', 'assignees')->paginate(15));
    }

    public function show($id)
    {
        try {
            return new BacklogResource(Backlog::findOrFail($id));
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Oops! Backlog not found.'], 404);
        }
    }

    public function showByProjectId($project)
    {

        $user = Auth::user();
        $user_id = $user->id;

        if ($user->role_id == 1 || $user->role_id == 2) {

            $data = DB::table('backlogprojectview')->where('project_id', $project)->paginate(9);
            return response()->json(['data' => $data], 200);
        }else {
            $member = DB::table('members')->where('user_id', $user_id)->where('project_id', $project)->first();

            $member_id = $member->id;
            $data_raw = DB::table('assignees')->where('member_id', $member_id)->get();

            $backlog_id = [];

            foreach ($data_raw as $value) {
                array_push($backlog_id, $value->backlog_id);
            }

            $data = DB::table('backlogprojectview')->where('project_id', $project)->whereIn('id', $backlog_id)->paginate(9);
            return response()->json(['data' => $data], 200);
        }
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        try {
            if ($user->role_id == 1 || $user->role_id == 2) {
                $backlog = new Backlog;
                $backlog->title = $request->input('title');
                $backlog->story = $request->input('story');
                $backlog->acceptance_criteria = $request->input('acceptance_criteria');
                $backlog->type_id = $request->input('type_id');
                $backlog->project_id = $request->input('project_id');
                $backlog->priority_id = $request->input('priority_id');
                $backlog->start_date = $request->input('start_date');
                $backlog->end_date = $request->input('duration');
                $backlog->save();

                return new BacklogResource($backlog);
            } else {
                return response()->json(['error' => 'You must be a project manager to create a backlog.'], 409);
            }

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error occured. Please try again'], 409);
        }
    }

    public function edit()
    {
        # code...
    }

    public function update(Request $request)
    {

        $auth_user = Auth::user()->first_name .' '. Auth::user()->last_name;
        $backlog_id = $request->input('id');

        $backlog = Backlog::findOrFail($backlog_id);

        $start_date_raw = $request->input('start_date');
        $duration_raw = $request->input('duration');


        // get assignees emails

        $emails = [];

        $assignee_id = [];

        $assignees = DB::table('assignees')->where('backlog_id', $backlog_id)->get();

        foreach ($assignees as $user_id) {
           array_push($assignee_id, $user_id->user_id);
        }

        $users = DB::table('users')->whereIn('id', $assignee_id)->get();

        foreach ($users as  $email) {
            array_push($emails, $email->email);
        }
        // getting project manager email
        $project = DB::table('projects')->where('id', $backlog->project_id)->first();

        $manager = DB::table('users')->where('id', $project->manager_id)->first();

        // final emails to notify
        array_push($emails, $manager->email);

        try {
            $backlog->start_date = $request->input('start_date');
            $backlog->end_date = $request->input('duration');

            $backlog->save();

            $to = $emails;

            $subject = 'BACKLOG TIMELINE UPDATE';

            $body = '
                <p>Good day</p> <br/>'.
                'Backlog '.$backlog->title. ' timelines has been updated by '.$auth_user.'.  <br/> <br/>
                <table width="90%" border="0">
                <tr>
                <td><b>Start Date:</b></td> <td>'.$start_date_raw.'</td>
                </tr>
                <tr>
                <td><b>Duration:</b></td> <td>'.$duration_raw.'(hours)</td>
                </tr>
                </table>  <br/> <br/>
                <strong>Regards,</strong>
                 <br/>
                 FlexiPMS Team' ;

            $mailer = new MailerCustom;

            $mailer->mailer($to, $subject, $body);
            return new BacklogResource($backlog);

        } catch (\Throwable $th) {

            return response()->json(["error" => "Error Occured"], 409);
        }

    }
}
