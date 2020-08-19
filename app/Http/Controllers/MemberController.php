<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Member;
use App\MailerCustom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProjectResource;

class MemberController extends Controller
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

    public function create(Request $request)
    {
        $id = $request->input('user_id');
        $project = $request->input('project_id');

        $project_raw = DB::table('projects')->where('id', $project)->first();

        try {
            $user = User::findOrFail($id);
            $full_name = $user->first_name.' '.$user->last_name;
            $role = Role::findOrFail($user->role_id);
            $role_description = $role->role_description;

            $member = new Member;

            $member->project_id = $project;
            $member->full_name = $full_name;
            $member->role = $role_description;
            $member->user_id = $id;

            $member->save();

             // notify user with email

            $mailer = new MailerCustom;

            $to = array(
                $user->email
            );

            $subject = 'YOU HAVE BEEN ADDED TO A PROJECT';

            $body = '
                Good day
                <br/> <br/>

                You have been added to a project ('. $project_raw->name.').
                <br/> <br/>

                <strong>Regards, </strong>
                <br/>
                FlexiPMS Team
            ';

            $mailer->mailer($to, $subject, $body);

            return response()->json($member, 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error occured. Please try again'], 409);
        }

    }

    public function delete($id)
    {
        try {
            $member = Member::findOrFail($id);

            $project_raw = DB::table('projects')->where('id', $member->project_id)->first();

            $user = DB::table('users')->where('id', $member->user_id)->first();

            $data_raw = $member->project_id;

            $member->delete();

            // notify user
            $mailer = new MailerCustom;

            $to = array(
                $user->email
            );

            $subject = 'YOU HAVE BEEN REMOVED FROM A PROJECT';

            $body = '
                Good day
                <br/> <br/>

                You have been removed from a project ('. $project_raw->name.').
                <br/> <br/>

                <strong>Regards, </strong>
                <br/>
                FlexiPMS Team
            ';

            $mailer->mailer($to, $subject, $body);

            $data = DB::table('members')->where('project_id', $data_raw)->get();
            return response()->json($data, 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Member not found. Please try again'], 404);
        }
    }
}
