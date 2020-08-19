<?php

namespace App\Http\Controllers;

use App\Assignee;
use App\MailerCustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function create(Request $request)
    {
        try {
            $assignee = new Assignee;
            $member = DB::table('members')->where('id', $request->input('member_id'))->first();

            $user = DB::table('users')->where('id', $member->user_id)->first();
            $role = $member->role;
            $full_name = $user->first_name.' '.$user->last_name;

            $assignee->member_id = $member->id;
            $assignee->user_id = $member->user_id;
            $assignee->project_id = $member->project_id;
            $assignee->backlog_id = $request->input('backlog_id');
            $assignee->full_name = $full_name;
            $assignee->role = $role;

            $assignee->save();


            // get backlog name

            $backlog = DB::table('backlogs')->where('id', $request->input('backlog_id'))->first();

            $backlog_name = $backlog->title;

            // notify user with email

            $mailer = new MailerCustom;

            $to = array(
                $user->email
            );

            $subject = 'YOU HAVE BEEN ASSIGNED TO A NEW TASK';

            $body = '
                Good day
                <br/> <br/>

                You have been assigned to a new task ('. $backlog_name.'). <br/>
                Login on http://localhost:3000 and add or update timelines.
                <br/> <br/>

                <strong>Regards, </strong>
                <br/>
                FlexiPMS Team
            ';

            $mailer->mailer($to, $subject, $body);

            return response()->json($assignee, 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Project member or user not found. Please try again'], 404);
        }

    }

    public function delete($id)
    {
        try {
            $member = Assignee::findOrFail($id);

            $user = DB::table('users')->where('id', $member->user_id)->first();

            $backlog_id = $member->backlog_id;

            $member->delete();

            $data = DB::table('assignees')->where('backlog_id', $backlog_id)->get();

            $backlog = DB::table('backlogs')->where('id', $backlog_id)->first();

            $backlog_name = $backlog->title;

            // notify user with email

            $mailer = new MailerCustom;

            $to = array(
                $user->email
            );

            $subject = 'YOU HAVE BEEN REMOVED FROM TASK ASSIGNEES';

            $body = '
                Good day
                <br/> <br/>

                You have been removed from a task ('. $backlog_name.') you were assigned.
                <br/> <br/>

                <strong>Regards, </strong>
                <br/>
                FlexiPMS Team
            ';

            $mailer->mailer($to, $subject, $body);

            return response()->json($data, 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Assignee not found. Please try again'], 404);
        }
    }

}
